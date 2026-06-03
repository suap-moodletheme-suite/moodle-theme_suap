<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.

function theme_suap_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_suap', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_suap and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/suap/scss/pre.scss');

    // Pre SCSS customizado via interface
    $precustom = !empty($theme->settings->rawscsspre) ? $theme->settings->rawscsspre : '';

    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/suap/scss/post.scss');

    // Post SCSS customizado via interface
    $postcustom = !empty($theme->settings->rawscss) ? $theme->settings->rawscss : '';

    // Combine them together.
    return $pre . "\n" . $precustom . "\n" . $scss . "\n" . $post . "\n" . $postcustom;
}

// Essa função é responsável por transformar uma configtextarea(label, link, icon, target e capabilities) em um objeto.
function parse_configtextarea_string($config_string) {
    $default_value = 'N/A';
    $lines = explode("\n", trim($config_string));
    $result = [];

    foreach ($lines as $line) {
        $parts = preg_split('/\|/', $line);

        foreach ($parts as &$part) {
            $part = trim($part);
            if (empty($part)) {
                $part = $default_value;
            }
        }

        if (strpos($parts[0], ',') !== false) {
            $array_label = explode(',', $parts[0]);
            $parts[0] = get_string($array_label[0], $array_label[1]);
        }

        $result[] = [
            'label' => $parts[0],
            'link' => $parts[1],
            'icon' => $parts[2],
            'target' => $parts[3],
            'capabilities' => $parts[4],
        ];
    }

    return $result;
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 * @package theme_suap
 */
function theme_suap_user_preferences(): array {
    return [
        'visual_preference' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_counter_close' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_index_drawer_open' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_blocks_drawer_open' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_dyslexia_friendly' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_remove_justify' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_highlight_links' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_stop_animations' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_hidden_illustrative_image' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_big_cursor' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_vlibras_active' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_high_line_height' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_zoom_level' => [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 100,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_suap_accessibility_color_mode' => [
            'type' => PARAM_ALPHANUMEXT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 'default',
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}


/**
 * Retorna as classes de acessibilidade baseadas nas preferências do usuário.
 *
 * @param stdClass $user O objeto de usuário atual (geralmente $USER).
 * @return array Lista de classes CSS para aplicar no <body>.
 * @package theme_suap
 */
function theme_suap_get_accessibility_classes($user) {
    global $USER;

    $classes = [];
    $prefs = theme_suap_user_preferences();
    $accessibility_prefs = array_keys($prefs);

    foreach ($accessibility_prefs as $prefname) {
        // pula preferências que não são de acessibilidade
        if (strpos($prefname, 'theme_suap_accessibility_') !== 0) {
            continue;
        }

        $default = $prefs[$prefname]['default'] ?? false;
        $value = get_user_preferences($prefname, $default, $USER->id);

        // Caso especial: modo de cor (string, não booleano)
        if ($prefname === 'theme_suap_accessibility_color_mode') {
            if ($value && $value !== 'default') {
                $classes[] = 'accessibility_color_mode_' . $value;
            }
            continue;
        }

        $normalized = ($value === true || $value === "true" || $value === 1 || $value === "1");

        if ($normalized) {
            // Gera uma classe CSS com nome limpo, ex.: theme_suap_accessibility_big_cursor → accessibility_big-cursor
            $classname = str_replace('theme_suap_', '', $prefname);
            $classes[] = $classname;
        }
    }

    return $classes;
}




/**
 * Adiciona itens específicos de administrador ao menu de usuário.
 *
 * @param array $items O array de itens para adicionar os links.
 * @return array O array atualizado com os itens de administrador.
 * @package theme_suap
 */
function theme_suap_add_admin_items_user_menu(): ?array {
    global $CFG;
    $items = [];
    if (is_siteadmin()) {
        $items[] = [
            'link' => [
                'title' => get_string('administrationsite', 'core'),
                'url' => $CFG->wwwroot . '/admin/search.php',
            ],
        ];

        $items[] = [
            'link' => [
                'title' => get_string('mycourses', 'core'),
                'url' => $CFG->wwwroot . '/my/courses.php',
            ],
        ];
    }

    return $items;
}

function theme_suap_is_contentbutton_active() {
    global $PAGE;
    $context_now = $PAGE->context;

    if ($context_now->contextlevel !== CONTEXT_SYSTEM) {
        return true;
    }

    return false;
}
