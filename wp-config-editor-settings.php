<?php

// Sécurisation: empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définir des constantes pour le chemin du plugin
define('WPCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPCE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Enregistrer l'admin menu
add_action('admin_menu', 'register_wp_config_page');

// Enqueue styles pour la page d'administration
add_action('admin_enqueue_scripts', 'enqueue_wpce_admin_styles');

function enqueue_wpce_admin_styles($hook) {
    if ($hook == 'settings_page_wp_config_page') {
        wp_enqueue_style('wpce-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
        wp_enqueue_script('wpce-admin-script', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), null, true);
    }
}


/**
 * Enregistre une nouvelle page d'options dans l'administration WordPress.
 */
function register_wp_config_page() {
    add_options_page(
        'Modifier le fichier wp-config.php',
        'WP Config Editor',
        'manage_options',
        'wp_config_page',
        'display_wp_config_page'
    );
}

/**
 * Affiche la page pour éditer le fichier wp-config.php
 */
function display_wp_config_page() {
    // Vérifie si l'utilisateur a les capacités nécessaires
    if (!current_user_can('administrator')) {
        return;
    }

    // Définir le chemin du fichier wp-config.php
    $wp_config_file = ABSPATH . 'wp-config.php';

    // Initialiser le contenu de wp-config.php comme vide par défaut
    $wp_config_content = "";

    // Vérifier l'existence du fichier wp-config.php avant lecture ou modification
    if (file_exists($wp_config_file)) {
        $wp_config_content = file_get_contents($wp_config_file);
    } else {
        echo '<div class="error"><p>Le fichier wp-config.php est introuvable.</p></div>';
        return;
    }

    // Traiter les modifications du formulaire principal
    if (isset($_POST['wp_config_content'])) {
        $wp_config_content = stripslashes($_POST['wp_config_content']);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le fichier wp-config.php a été modifié avec succès.</p></div>';
    }

    // Traiter les suggestions
    if (isset($_POST['wp_memory_limit'])) {
        $wp_memory_limit = $_POST['wp_memory_limit'] === 'other' ? intval($_POST['wp_memory_limit_other']) : intval($_POST['wp_memory_limit']);
        if ($wp_memory_limit > 0) {
            if (strpos($wp_config_content, "define('WP_MEMORY_LIMIT'") === false) {
                $wp_config_content .= "\ndefine('WP_MEMORY_LIMIT', '{$wp_memory_limit}M');";
            } else {
                $wp_config_content = preg_replace("/define\('WP_MEMORY_LIMIT', '(.*?)M'\);/", "define('WP_MEMORY_LIMIT', '{$wp_memory_limit}M');", $wp_config_content);
            }
            file_put_contents($wp_config_file, $wp_config_content);
            echo '<div class="updated"><p>La mémoire PHP de WordPress a été modifiée avec succès.</p></div>';
        }
    }
    if (isset($_POST['wp_memory_limit_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_MEMORY_LIMIT', '(.*?)M'\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La mémoire PHP de WordPress a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_debug_suggestion'])) {
        $wp_debug = $_POST['wp_debug'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('WP_DEBUG'") === false) {
            $wp_config_content .= "\ndefine('WP_DEBUG', $wp_debug);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_DEBUG', (.*?)\);/", "define('WP_DEBUG', $wp_debug);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le mode débogage de WordPress a été modifié avec succès.</p></div>';
    }
    if (isset($_POST['wp_debug_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_DEBUG', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le mode débogage de WordPress a été supprimé avec succès.</p></div>';
    }

    if (isset($_POST['wp_post_revisions_suggestion'])) {
        $wp_post_revisions = intval($_POST['wp_post_revisions']);
        if ($wp_post_revisions >= 0) {
            if (strpos($wp_config_content, "define('WP_POST_REVISIONS'") === false) {
                $wp_config_content .= "\ndefine('WP_POST_REVISIONS', $wp_post_revisions);";
            } else {
                $wp_config_content = preg_replace("/define\('WP_POST_REVISIONS', (.*?)\);/", "define('WP_POST_REVISIONS', $wp_post_revisions);", $wp_config_content);
            }
            file_put_contents($wp_config_file, $wp_config_content);
            echo '<div class="updated"><p>Le nombre de révisions de post a été modifié avec succès.</p></div>';
        }
    }
    if (isset($_POST['wp_post_revisions_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_POST_REVISIONS', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le nombre de révisions de post a été supprimé avec succès.</p></div>';
    }

    if (isset($_POST['autosave_interval_suggestion'])) {
        $autosave_interval = intval($_POST['autosave_interval']);
        if ($autosave_interval > 0) {
            if (strpos($wp_config_content, "define('AUTOSAVE_INTERVAL'") === false) {
                $wp_config_content .= "\ndefine('AUTOSAVE_INTERVAL', $autosave_interval);";
            } else {
                $wp_config_content = preg_replace("/define\('AUTOSAVE_INTERVAL', (.*?)\);/", "define('AUTOSAVE_INTERVAL', $autosave_interval);", $wp_config_content);
            }
            file_put_contents($wp_config_file, $wp_config_content);
            echo '<div class="updated"><p>L\'intervalle d\'enregistrement automatique a été modifié avec succès.</p></div>';
        }
    }
    if (isset($_POST['autosave_interval_remove'])) {
        $wp_config_content = preg_replace("/define\('AUTOSAVE_INTERVAL', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>L\'intervalle d\'enregistrement automatique a été supprimé avec succès.</p></div>';
    }

    if (isset($_POST['wp_cache_suggestion'])) {
        $wp_cache = $_POST['wp_cache'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('WP_CACHE'") === false) {
            $wp_config_content .= "\ndefine('WP_CACHE', $wp_cache);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_CACHE', (.*?)\);/", "define('WP_CACHE', $wp_cache);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le cache de WordPress a été modifié avec succès.</p></div>';
    }
    if (isset($_POST['wp_cache_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_CACHE', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>Le cache de WordPress a été supprimé avec succès.</p></div>';
    }

    if (isset($_POST['wp_siteurl_suggestion'])) {
        $wp_siteurl = esc_url_raw($_POST['wp_siteurl']);
        if (!empty($wp_siteurl)) {
            if (strpos($wp_config_content, "define('WP_SITEURL'") === false) {
                $wp_config_content .= "\ndefine('WP_SITEURL', '$wp_siteurl');";
            } else {
                $wp_config_content = preg_replace("/define\('WP_SITEURL', '(.*?)'\);/", "define('WP_SITEURL', '$wp_siteurl');", $wp_config_content);
            }
            file_put_contents($wp_config_file, $wp_config_content);
            echo '<div class="updated"><p>La constante WP_SITEURL a été modifiée avec succès.</p></div>';
        }
    }
    if (isset($_POST['wp_siteurl_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_SITEURL', '(.*?)'\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_SITEURL a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_home_suggestion'])) {
        $wp_home = esc_url_raw($_POST['wp_home']);
        if (!empty($wp_home)) {
            if (strpos($wp_config_content, "define('WP_HOME'") === false) {
                $wp_config_content .= "\ndefine('WP_HOME', '$wp_home');";
            } else {
                $wp_config_content = preg_replace("/define\('WP_HOME', '(.*?)'\);/", "define('WP_HOME', '$wp_home');", $wp_config_content);
            }
            file_put_contents($wp_config_file, $wp_config_content);
            echo '<div class="updated"><p>La constante WP_HOME a été modifiée avec succès.</p></div>';
        }
    }
    if (isset($_POST['wp_home_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_HOME', '(.*?)'\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_HOME a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['display_errors_suggestion'])) {
        $display_errors = $_POST['display_errors'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('DISPLAY_ERRORS'") === false) {
            $wp_config_content .= "\ndefine('DISPLAY_ERRORS', $display_errors);";
        } else {
            $wp_config_content = preg_replace("/define\('DISPLAY_ERRORS', (.*?)\);/", "define('DISPLAY_ERRORS', $display_errors);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante DISPLAY_ERRORS a été modifiée avec succès.</p></div>';
    }
    if (isset($_POST['display_errors_remove'])) {
        $wp_config_content = preg_replace("/define\('DISPLAY_ERRORS', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante DISPLAY_ERRORS a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_auto_update_plugin_suggestion'])) {
        $wp_auto_update_plugin = $_POST['wp_auto_update_plugin'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('WP_AUTO_UPDATE_PLUGIN'") === false) {
            $wp_config_content .= "\ndefine('WP_AUTO_UPDATE_PLUGIN', $wp_auto_update_plugin);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_PLUGIN', (.*?)\);/", "define('WP_AUTO_UPDATE_PLUGIN', $wp_auto_update_plugin);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La mise à jour automatique des plugins a été modifiée avec succès.</p></div>';
    }
    if (isset($_POST['wp_auto_update_plugin_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_PLUGIN', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La mise à jour automatique des plugins a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_auto_update_theme_suggestion'])) {
        $wp_auto_update_theme = $_POST['wp_auto_update_theme'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('WP_AUTO_UPDATE_THEME'") === false) {
            $wp_config_content .= "\ndefine('WP_AUTO_UPDATE_THEME', $wp_auto_update_theme);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_THEME', (.*?)\);/", "define('WP_AUTO_UPDATE_THEME', $wp_auto_update_theme);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La mise à jour automatique des thèmes a été modifiée avec succès.</p></div>';
    }
    if (isset($_POST['wp_auto_update_theme_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_THEME', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La mise à jour automatique des thèmes a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_auto_update_core_suggestion'])) {
        $wp_auto_update_core = $_POST['wp_auto_update_core'];
        if (strpos($wp_config_content, "define('WP_AUTO_UPDATE_CORE'") === false) {
            $wp_config_content .= "\ndefine('WP_AUTO_UPDATE_CORE', $wp_auto_update_core);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_CORE', (.*?)\);/", "define('WP_AUTO_UPDATE_CORE', $wp_auto_update_core);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_AUTO_UPDATE_CORE a été modifiée avec succès.</p></div>';
    }
    if (isset($_POST['wp_auto_update_core_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_AUTO_UPDATE_CORE', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_AUTO_UPDATE_CORE a été supprimée avec succès.</p></div>';
    }

    if (isset($_POST['wp_debug_display_suggestion'])) {
        $wp_debug_display = $_POST['wp_debug_display'] === '1' ? 'true' : 'false';
        if (strpos($wp_config_content, "define('WP_DEBUG_DISPLAY'") === false) {
            $wp_config_content .= "\ndefine('WP_DEBUG_DISPLAY', $wp_debug_display);";
        } else {
            $wp_config_content = preg_replace("/define\('WP_DEBUG_DISPLAY', (.*?)\);/", "define('WP_DEBUG_DISPLAY', $wp_debug_display);", $wp_config_content);
        }
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_DEBUG_DISPLAY a été modifiée avec succès.</p></div>';
    }
    if (isset($_POST['wp_debug_display_remove'])) {
        $wp_config_content = preg_replace("/define\('WP_DEBUG_DISPLAY', (.*?)\);/", "", $wp_config_content);
        file_put_contents($wp_config_file, $wp_config_content);
        echo '<div class="updated"><p>La constante WP_DEBUG_DISPLAY a été supprimée avec succès.</p></div>';
    }

    // Déterminer la mémoire PHP actuelle
    preg_match("/define\('WP_MEMORY_LIMIT', '(.*?)M'\);/", $wp_config_content, $matches);
    $current_memory_limit = isset($matches[1]) ? intval($matches[1]) : 64;

    // Déterminer le nombre actuel de révisions de post
    preg_match("/define\('WP_POST_REVISIONS', (.*?)\);/", $wp_config_content, $matches);
    $current_post_revisions = isset($matches[1]) ? intval($matches[1]) : -1;

    // Déterminer l'intervalle d'enregistrement automatique actuel
    preg_match("/define\('AUTOSAVE_INTERVAL', (.*?)\);/", $wp_config_content, $matches);
    $current_autosave_interval = isset($matches[1]) ? intval($matches[1]) : 60;

    ?>
    <div class="wrap">
        <script>
        window.onload = function() {
            var textarea = document.getElementById('wp_config_content');
            textarea.scrollTop = textarea.scrollHeight;
        }
        </script>
        <h1>Modifier wp-config.php</h1>
        <p>Bienvenue dans l'éditeur de configuration <b>WP Config Editor</b>.<br><br> Cet outil vous permet de modifier facilement le fichier de configuration principal de WordPress <b>wp-config.php</b> directement depuis l'interface d'administration.<br>Vous pouvez effectuer des modifications critiques telles que la gestion de la mémoire PHP, l'activation du mode débogage, la configuration des révisions de posts, et bien plus encore.<br><br><i>Utilisez les suggestions à droite pour ajouter ou supprimer rapidement des paramètres couramment utilisés</i></p>
        <div style="display: flex;">
            <div style="flex: 0;">
                <form method="post" id="wp_config_form">
                    <textarea name="wp_config_content" id="wp_config_content" cols="80" rows="20"><?php echo esc_textarea($wp_config_content); ?></textarea>
                    <div id="validation_errors" style="color: red; margin-top: 10px;"></div>
                    <input type="submit" value="Enregistrer les modifications" class="button-primary" style="margin-top: 20px;">
                </form>
            </div>
            <div style="flex: 1; margin-left: 20px;">
                <h2>Suggestions</h2>
                <form method="post" id="wp_suggestions_form">
                    <label for="wp_memory_limit">Mémoire PHP de WordPress (en Mo):<span class="dashicons dashicons-info" data-info="La mémoire PHP détermine la quantité de mémoire allouée à WordPress. Augmenter la mémoire peut être nécessaire pour des sites avec beaucoup de plugins ou un thème complexe. Cependant, ne dépassez pas la limite imposée par votre hébergeur."></span></label>
                    <select name="wp_memory_limit" id="wp_memory_limit" onchange="toggleOtherInput()">
                        <option value="64" <?php selected($current_memory_limit, 64); ?>>64</option>
                        <option value="128" <?php selected($current_memory_limit, 128); ?>>128</option>
                        <option value="256" <?php selected($current_memory_limit, 256); ?>>256</option>
                        <option value="512" <?php selected($current_memory_limit, 512); ?>>512</option>
                        <option value="1024" <?php selected($current_memory_limit, 1024); ?>>1024</option>
                        <option value="2048" <?php selected($current_memory_limit, 2048); ?>>2048</option>
                        <option value="other">Autre</option>
                    </select>
                    <input type="number" name="wp_memory_limit_other" id="wp_memory_limit_other" value="" placeholder="Entrer une valeur" style="display: none;">
                    <input type="submit" name="wp_memory_limit_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_memory_limit_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="wp_debug">Activer le mode Débogage:<span class="dashicons dashicons-info" data-info="Le mode débogage affiche les erreurs de PHP, avis et avertissements directement sur votre site web. Activez-le pour identifier et résoudre les problèmes, mais désactivez-le sur un site en production pour éviter de montrer des informations sensibles aux visiteurs."></span></label>
                    <select name="wp_debug" id="wp_debug">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="wp_debug_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_debug_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="wp_post_revisions">Nombre max de révisions de post<span class="dashicons dashicons-info" data-info="WP_POST_REVISIONS limite le nombre de révisions de chaque post. Les révisions permettent de restaurer des versions précédentes des articles, mais trop de révisions peuvent encombrer votre base de données. Mettez 0 pour désactiver les révisions ou un nombre élevé si vous faites beaucoup de modifications."></span></label>
                    <input type="number" name="wp_post_revisions" id="wp_post_revisions" value="<?php echo esc_attr($current_post_revisions); ?>">
                    <input type="submit" name="wp_post_revisions_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_post_revisions_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="autosave_interval">Intervalle d'enregistrement auto (en sec):<span class="dashicons dashicons-info" data-info="AUTOSAVE_INTERVAL configure la fréquence de l'enregistrement automatique des brouillons. Définissez une valeur plus basse pour enregistrer plus fréquemment, ce qui est utile si vous avez des problèmes de perte de données. Une valeur plus élevée réduit la fréquence des écritures en base de données."></span></label>
                    <input type="number" name="autosave_interval" id="autosave_interval" value="<?php echo esc_attr($current_autosave_interval); ?>">
                    <input type="submit" name="autosave_interval_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="autosave_interval_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="wp_cache">Activer le cache:<span class="dashicons dashicons-info" data-info="WP_CACHE permet d'activer ou désactiver le cache de WordPress, ce qui peut améliorer les performances de votre site en réduisant la charge sur le serveur. Activez cette option seulement si vos plugins de cache sont configurés correctement."></span></label>
                    <select name="wp_cache" id="wp_cache">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="wp_cache_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_cache_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="wp_siteurl">URL du site (WP_SITEURL):<span class="dashicons dashicons-info" data-info="WP_SITEURL spécifie l'adresse URL de votre site WordPress. Cela peut être utile lors de la migration de votre site ou pour forcer WordPress à utiliser une URL spécifique."></span></label>
                    <input type="text" name="wp_siteurl" id="wp_siteurl" value="">
                    <input type="submit" name="wp_siteurl_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_siteurl_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="wp_home">URL de l'accueil (WP_HOME):<span class="dashicons dashicons-info" data-info="WP_HOME spécifie l'adresse URL de l'accueil de votre site WordPress. Cela peut être utile lors de la migration de votre site ou pour forcer WordPress à utiliser une URL d'accueil spécifique."></span></label>
                    <input type="text" name="wp_home" id="wp_home" value="">
                    <input type="submit" name="wp_home_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_home_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="display_errors">Afficher les erreurs PHP (DISPLAY_ERRORS):<span class="dashicons dashicons-info" data-info="DISPLAY_ERRORS définit si les erreurs PHP doivent être affichées à l'écran. Il est recommandé de désactiver cette option sur les sites en production pour des raisons de sécurité."></span></label>
                    <select name="display_errors" id="display_errors">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="display_errors_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="display_errors_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="wp_auto_update_core">Mises à jour automatiques du cœur (WP_AUTO_UPDATE_CORE):<span class="dashicons dashicons-info" data-info="WP_AUTO_UPDATE_CORE permet de configurer les mises à jour automatiques du cœur de WordPress. Par défaut, seules les mises à jour mineures sont appliquées automatiquement. Vous pouvez les désactiver ou permettre toutes les mises à jour automatiques."></span></label>
                    <select name="wp_auto_update_core" id="wp_auto_update_core">
                        <option value="true">Activer toutes les mises à jour automatiques</option>
                        <option value="false">Désactiver toutes les mises à jour automatiques</option>
                        <option value="'minor'">Activer uniquement les mises à jour mineures</option>
                    </select>
                    <input type="submit" name="wp_auto_update_core_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_auto_update_core_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="auth_key">Clé de sécurité AUTH_KEY:<span class="dashicons dashicons-info" data-info="AUTH_KEY est une clé de sécurité utilisée pour chiffrer les informations de session. Assurez-vous de définir une clé unique et complexe."></span></label>
                    <input type="text" name="auth_key" id="auth_key" value="">
                    <input type="submit" name="auth_key_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="auth_key_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="secure_auth_key">Clé de sécurité SECURE_AUTH_KEY:<span class="dashicons dashicons-info" data-info="SECURE_AUTH_KEY est une clé de sécurité utilisée pour chiffrer des informations spécifiques de session. Assurez-vous de définir une clé unique et complexe."></span></label>
                    <input type="text" name="secure_auth_key" id="secure_auth_key" value="">
                    <input type="submit" name="secure_auth_key_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="secure_auth_key_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="wp_auto_update_plugin">Activer les mises à jour automatiques des plugins (WP_AUTO_UPDATE_PLUGIN):<span class="dashicons dashicons-info" data-info="WP_AUTO_UPDATE_PLUGIN permet d'activer ou de désactiver les mises à jour automatiques de vos plugins."></span></label>
                    <select name="wp_auto_update_plugin" id="wp_auto_update_plugin">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="wp_auto_update_plugin_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_auto_update_plugin_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>

                    <label for="wp_auto_update_theme">Activer les mises à jour automatiques des thèmes (WP_AUTO_UPDATE_THEME):<span class="dashicons dashicons-info" data-info="WP_AUTO_UPDATE_THEME permet d'activer ou de désactiver les mises à jour automatiques de vos thèmes."></span></label>
                    <select name="wp_auto_update_theme" id="wp_auto_update_theme">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="wp_auto_update_theme_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_auto_update_theme_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                    
                    <label for="wp_debug_display">Afficher les erreurs de débogage (WP_DEBUG_DISPLAY):<span class="dashicons dashicons-info" data-info="WP_DEBUG_DISPLAY permet de contrôler si les erreurs de débogage doivent être affichées sur votre site ou non."></span></label>
                    <select name="wp_debug_display" id="wp_debug_display">
                        <option value="1">Activer</option>
                        <option value="0">Désactiver</option>
                    </select>
                    <input type="submit" name="wp_debug_display_suggestion" value="Ajouter" class="button-secondary">
                    <input type="submit" name="wp_debug_display_remove" value="Supprimer" class="button-secondary">
                    <br>
                    <br>
                </form>
            </div>
        </div>
    </div>
   
    <?php
}