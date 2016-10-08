<?php

/**
 * Class ClientDash_Page_Help_Tab_Server
 *
 * Adds the core content section for Help -> Server.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 */
class ClientDash_Core_Page_Help_Tab_Server extends ClientDash
{

    public $error_msg = 'Unable to get information.';

    /**
     * The main construct function.
     *
     * @since Client Dash 1.5
     */
    function __construct()
    {

        $this->add_content_section(array(
            'name' => 'Server Information',
            'page' => 'Help',
            'tab' => 'Server',
            'callback' => array($this, 'block_output')
        ));
    }

    /**
     * The content for the content section.
     *
     * @since Client Dash 1.4
     */
    public function block_output()
    {
        ?>
        <h3>Server information <?php echo function_exists('php_uname') ? php_uname('s') . ' ' . php_uname('v') . ' ' . php_uname('m') : '' ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Server software version</th>
                <td><?php echo function_exists('filter_input') ? filter_input(INPUT_SERVER, 'SERVER_SOFTWARE', FILTER_SANITIZE_STRING) : $_SERVER['SERVER_SOFTWARE'] ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">PHP version</th>
                <td><?php echo function_exists('phpversion') ? phpversion() : $this->error_msg ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Memory limit</th>
                <td><?php echo function_exists('ini_get') ? ini_get('memory_limit') : $this->error_msg; ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Your web browser</th>
                <td><?php echo function_exists('filter_input') ? filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING) : $_SERVER['HTTP_USER_AGENT'] ?></td>
            </tr>

        </table>
        <?php
    }
}