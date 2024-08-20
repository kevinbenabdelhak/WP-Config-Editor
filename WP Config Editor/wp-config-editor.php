<?php
/*
Plugin Name: WP Config Editor
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-config-editor/
Description: WP Config Editor est un plugin WordPress qui permet de modifier le fichier wp-config.php depuis une page d'option à l'aide d'un éditeur et de suggestions
Version: 1.0
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
Contributors: kevinbenabdelhak

*/

// Sécurisation: empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Inclure le fichier de paramètres du plugin
include_once plugin_dir_path(__FILE__) . 'wp-config-editor-settings.php';