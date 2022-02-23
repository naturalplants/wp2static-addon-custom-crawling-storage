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
    <input name="action" type="hidden" value="wp2static_custom_crawling_storage_save_options" />
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
                    for="<?php echo $view['options']['perpetuatedStoragePath']->name; ?>"
                ><?php echo $view['options']['perpetuatedStoragePath']->label; ?></label>
            </td>
            <td style="width:50%;">
                <input
                    id="<?php echo $view['options']['perpetuatedStoragePath']->name; ?>"
                    name="<?php echo $view['options']['perpetuatedStoragePath']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['perpetuatedStoragePath']->value !== '' ? $view['options']['perpetuatedStoragePath']->value : ''; ?>"
                    class="widefat"
                />
            </td>
            <td>
                <?php echo $view['options']['perpetuatedStoragePath']->description; ?>
            </td>
        </tr>

    </tbody>
</table>

<br>

    <button class="button btn-primary">Save Custom Crawler Storage Options</button>
</form>

