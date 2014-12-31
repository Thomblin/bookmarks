<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="Sebastian">

<title>Bookmarks</title>

<style type="text/css">
    <?php
        use Assetic\Asset\AssetCollection;
        use Assetic\Asset\FileAsset;

        $cssDir = realpath(__DIR__ . '/../public/css') . '/';

        $css = new AssetCollection(array(
            new FileAsset( $cssDir . 'overview.css'),
            new FileAsset( $cssDir . 'search.css'),
            new FileAsset( $cssDir . 'settings.css'),
        ));

        echo $css->dump();
    ?>
</style>