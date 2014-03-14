<?php

/*
Plugin Name: Fix Partial Encryption
Author: Rene Schmidt DevOps UG (haftungsbeschränkt) & Co. KG
Plugin URI: https://www.reneschmidt.de/
Description: Fixes "partial encryption" errors when using SSL/TLS/HTTPS. Removes schema from static URLs. For example, it changes "http://example.com" to "//example.com") so that Wordpress installations work flawlessly using HTTPS.
Version: 0.0.1
Licence: GPLv3
*/

class FixPartialEncryption
{

  /**
   * Initialize things
   */
  public function __construct()
  {
    /**
     * Skip in certain operation modes
     */
    if (defined('DOING_AJAX') || defined('DOING_CRON') || defined('APP_REQUEST') || defined('XMLRPC_REQUEST')) {
      return;
    }

    ob_start(array($this, 'obBufferRewrite'));
  }

  /**
   * Handle buffer rewrites
   *
   * @param String $buffer Buffer
   *
   * @return String Buffer
   */
  public function obBufferRewrite($buffer)
  {
    return $this->rewriteUris($buffer);
  }

  /**
   * Replace static URLs with hard-coded schema
   *
   * @param String $content Page content
   *
   * @return String
   */
  public function rewriteUris($content)
  {
    return preg_replace(
        "/ (href|src)=([\"'])(https?:\/\/)" . addslashes($_SERVER['HTTP_HOST']) . "([^\"']+)([\"'])/i",
        " $1=$2//" . $_SERVER['HTTP_HOST'] . "$4$5",
        $content
    );
  }
}

new FixPartialEncryption;