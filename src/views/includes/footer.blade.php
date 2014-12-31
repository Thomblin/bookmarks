<script language="javascript">
    <?php
        use Assetic\Asset\AssetCollection;
        use Assetic\Asset\FileAsset;

        $jsDir = realpath(__DIR__ . '/../public/js') . '/';

        $js = new AssetCollection(array(
            new FileAsset( $jsDir . 'jquery-2.1.1.min.js'),
            new FileAsset( $jsDir . 'BookmarkController.js'),
            new FileAsset( $jsDir . 'SearchController.js'),
            new FileAsset( $jsDir . 'MenuController.js'),
            new FileAsset( $jsDir . 'main.js'),
        ));


        echo $js->dump();
    ?>
</script>