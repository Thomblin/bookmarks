<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use \Behat\MinkExtension\Context\MinkContext;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var string
     */
    protected $resource;
    /**
     * @var PyStringNode
     */
    protected $requestPayload;
    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @mixed
     */
    protected $responsePayload;
    /**
     * The current scope within the response payload
     * which conditions are asserted against.^
     *
     * @var string
     */
    protected $scope;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $config = isset($parameters['guzzle']) && is_array($parameters['guzzle']) ? $parameters['guzzle'] : [];
        $config['base_url'] = $parameters['base_url'];
        $this->client = new Client($config);
    }

    /**
     * @Given /^I have the payload:$/
     */
    public function iHaveThePayload(PyStringNode $requestPayload)
    {
        $this->requestPayload = $requestPayload;
    }

    /**
     * @When /^I request "(GET|PUT|POST|DELETE) ([^"]*)"$/
     */
    public function iRequest($httpMethod, $resource)
    {
        $this->resource = $resource;
        $method = strtolower($httpMethod);
        try {
            switch ($httpMethod) {
                case 'PUT':
                    $this->response = $this
                        ->client
                        ->$method($resource, $this->requestPayload);
                    break;
                case 'POST':
                    $this->response = $this
                        ->client
                        ->$method($resource, ['body' => $this->getJsonPayload()]);
                    break;
                default:
                    $this->response = $this
                        ->client
                        ->$method($resource);
            }
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            // Sometimes the request will fail, at which point we have
            // no response at all. Let Guzzle give an error here, it's
            // pretty self-explanatory.
            if ($response === null) {
                throw $e;
            }
            $this->response = $e->getResponse();
        }
    }

    /**
     * treat payload as json data and try to decode it
     *
     * @return mixed
     */
    private function getJsonPayload()
    {
        return $this->getJsonFromPyStringNode($this->requestPayload);
    }

    /**
     * treat payload as json data and try to decode it
     *
     * @var PyStringNode $node
     *
     * @return mixed
     */
    private function getJsonFromPyStringNode(PyStringNode $node)
    {
        return \GuzzleHttp\json_decode(
            implode('', $node->getLines()),
            true
        );
    }

    /**
     * @Then /^I get a "([^"]*)" response$/
     */
    public function iGetAResponse($statusCode)
    {
        $response = $this->getResponse();
        $contentType = $response->getHeader('Content-Type');
        if ($contentType === 'application/json') {
            $bodyOutput = $response->getBody();
        } else {
            $bodyOutput = 'Output is ' . $contentType . ', which is not JSON and is therefore scary. Run the request manually.';
        }
        \PHPUnit_Framework_Assert::assertSame((int)$statusCode, (int)$this->getResponse()->getStatusCode(), $bodyOutput);
    }

    /**
     * Checks the response exists and returns it.
     *
     * @return Guzzle\Http\Message\Response
     * @throws Exception
     */
    protected function getResponse()
    {
        if (!$this->response) {
            throw new Exception("You must first make a request to check a response.");
        }
        return $this->response;
    }

    /**
     * return content of response
     *
     * @return string
     * @throws Exception
     */
    protected function getResponseContent()
    {
        return $this->getResponse()->getBody()->getContents();
    }

    /**
     * return content of response
     *
     * @return string
     * @throws Exception
     */
    protected function getJsonResponseContent()
    {
        return \GuzzleHttp\json_decode($this->getResponseContent());
    }

    /**
     * Return the response payload from the current response.
     *
     * @return mixed
     */
    protected function getResponsePayload()
    {
        if (!$this->responsePayload) {
            $json = json_decode($this->getResponse()->getBody(true));
            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = 'Failed to decode JSON body ';
                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $message .= '(Maximum stack depth exceeded).';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $message .= '(Underflow or the modes mismatch).';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $message .= '(Unexpected control character found).';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $message .= '(Syntax error, malformed JSON).';
                        break;
                    case JSON_ERROR_UTF8:
                        $message .= '(Malformed UTF-8 characters, possibly incorrectly encoded).';
                        break;
                    default:
                        $message .= '(Unknown error).';
                        break;
                }
                throw new Exception($message);
            }
            $this->responsePayload = $json;
        }
        return $this->responsePayload;
    }

    /**
     * @Given /^data is empty$/
     */
    public function dataIsEmpty()
    {
        $content = $this->getJsonResponseContent();

        \PHPUnit_Framework_Assert::assertEmpty($content, "'" .print_r($content, true) . "' is not empty'");
    }
}
