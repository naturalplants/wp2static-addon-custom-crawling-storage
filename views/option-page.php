<?php
// phpcs:disable Generic.Files.LineLength.MaxExceeded
// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @var mixed[] $view
 */
?>

<h2>Custom Crawling Storage Options</h2>

<form
        name="wp2static-s3-save-options"
        method="POST"
        action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

    <?php wp_nonce_field( $view['nonce_action'] ); ?>
    <input name="action" type="hidden" value="wp2static_custom_crawling_storage_save_options"/>

    <h3>For Crawled Site</h3>

    <table class="widefat striped">
        <tbody>

        <tr>
            <td nowrap="nowrap">
                <label
                        for="<?php echo $view['options']['crawlingStoragePath']->name; ?>"
                ><?php echo $view['options']['crawlingStoragePath']->label; ?></label>
            </td>
            <td style="width:50%;">
                <input
                        id="<?php echo $view['options']['crawlingStoragePath']->name; ?>"
                        name="<?php echo $view['options']['crawlingStoragePath']->name; ?>"
                        type="text"
                        value="<?php echo $view['options']['crawlingStoragePath']->value !== '' ? $view['options']['crawlingStoragePath']->value : ''; ?>"
                        class="widefat"
                />
            </td>
            <td>
                <?php echo $view['options']['crawlingStoragePath']->description; ?>
            </td>
        </tr>

        <tr>
            <td nowrap="nowrap">
                <label
                        for="<?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->name; ?>"
                ><?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->label; ?></label>
            </td>
            <td style="width:50%;">
                <input
                        id="<?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->name; ?>"
                        name="<?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->name; ?>"
                        type="text"
                        value="<?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->value !== '' ? $view['options']['perpetuatedStoragePathForCrawledSite']->value : ''; ?>"
                        class="widefat"
                />
            </td>
            <td>
                <?php echo $view['options']['perpetuatedStoragePathForCrawledSite']->description; ?>
            </td>
        </tr>

        </tbody>
    </table>

    <h3>For Post Processed Site</h3>

    <table class="widefat striped">
        <tbody>

        <tr>
            <td nowrap="nowrap">
                <label
                        for="<?php echo $view['options']['processingStoragePath']->name; ?>"
                ><?php echo $view['options']['processingStoragePath']->label; ?></label>
            </td>
            <td style="width:50%;">
                <input
                        id="<?php echo $view['options']['processingStoragePath']->name; ?>"
                        name="<?php echo $view['options']['processingStoragePath']->name; ?>"
                        type="text"
                        value="<?php echo $view['options']['processingStoragePath']->value !== '' ? $view['options']['processingStoragePath']->value : ''; ?>"
                        class="widefat"
                />
            </td>
            <td>
                <?php echo $view['options']['processingStoragePath']->description; ?>
            </td>
        </tr>

        <tr>
            <td nowrap="nowrap">
                <label
                        for="<?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->name; ?>"
                ><?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->label; ?></label>
            </td>
            <td style="width:50%;">
                <input
                        id="<?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->name; ?>"
                        name="<?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->name; ?>"
                        type="text"
                        value="<?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->value !== '' ? $view['options']['perpetuatedStoragePathForProcessedSite']->value : ''; ?>"
                        class="widefat"
                />
            </td>
            <td>
                <?php echo $view['options']['perpetuatedStoragePathForProcessedSite']->description; ?>
            </td>
        </tr>

        </tbody>
    </table>

    <br>

    <button class="button btn-primary">Save Custom Crawler Storage Options</button>
</form>

