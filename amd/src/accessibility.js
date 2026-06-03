// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Controls the notification drawer
 * Has the same function of message_popup/notification_popover_controller
 *
 * @package
 * @copyright  2024 IFRN DEAD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["core/str", "core_user/repository", "core/config"], function(str, Repository, Config) {

    /**
     *
     */
    function activeAccessibility() {
        let checkboxes = document.querySelectorAll('.custom-checkbox-access input[type="checkbox"]');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (event) => {
                let target = event.target;
                const checked = target.checked;
                const className = `accessibility_${target.id}`;
                const prefName = `theme_suap_accessibility_${target.id}`;

                if (checked) {
                    document.body.classList.add(className);
                } else {
                    document.body.classList.remove(className);
                }

                Repository.setUserPreference(prefName, checked);
                syncPreference(target.id, checked);
            });
        });
    }

    /**
     * Sincroniza as preferências de acessibilidade
     *
     * @param {string} key A chave da preferência
     * @param {boolean|string|number} value O valor a ser salvo
     */
    function syncPreference(key, value) {

        const encodedValue = typeof value === 'boolean'
            ? (value ? 'true' : 'false')
            : value;

        const url = Config.wwwroot + '/local/suap/api/index.php?sync_user_preference'
                + '&category=accessibility'
                + '&key=' + key
                + '&value=' + encodeURIComponent(encodedValue)
                + '&sesskey=' + Config.sesskey;

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(resp => resp.json())
        .catch(() => {
            // Falha silenciosa para cumprir a regra no-console
        });
    }


    const container = document.getElementById('selector-cycle-access');
    const zoomValue = document.getElementById('zoom-value');
    const indicators = document.getElementById('cycle-indicators');
    const button = document.getElementById('cycle-toggle');

    const colorContainer = document.getElementById('selector-cycle-color');
    const colorLabel = document.getElementById('color-mode-label');
    const colorIndicators = document.getElementById('color-indicators');
    const colorButton = document.getElementById('color-mode-toggle');

    let colorPreferences = {
        color_mode: 'default',
        color_mode_options: ['default', 'high_contrast', 'low_contrast', 'colorblind', 'grayscale'],
    };

    let preferences = {
        zoom_level: 100,
        zoom_options: [100, 120, 130, 150, 160]
    };

    Repository.getUserPreference('theme_suap_accessibility_zoom_level').then(value => {
        if (value) {
            preferences.zoom_level = parseInt(value);
        }

        button.addEventListener('click', cycleAccessibility);

        renderZoom();
    });

    Repository.getUserPreference('theme_suap_accessibility_color_mode').then(value => {
        if (value) {
            colorPreferences.color_mode = value;
        }

        colorButton.addEventListener('click', cycleColorMode);

        renderColorMode();
    });


    /**
     *
     */
    function renderZoom() {
        // Atualizar classe ativa do container
        if (preferences.zoom_level > 100) {
            container.classList.add('active');
        } else {
            container.classList.remove('active');
        }

        // Atualizar valor do zoom
        zoomValue.textContent = preferences.zoom_level + '%';

        // Renderizar indicadores
        indicators.innerHTML = ''; // Limpar antes de recriar
        preferences.zoom_options
        .filter(level => level > 100)
        .forEach(level => {
            const span = document.createElement('span');
            span.classList.add('cycle-indicator');
            if (level <= preferences.zoom_level) {
                span.classList.add('active');
            }
            indicators.appendChild(span);
        });
    }

    /**
     *
     */
    function renderColorMode() {
        const mode = colorPreferences.color_mode;

        // Atualiza o rótulo
        const labels = {
            "default": 'Padrão',
            high_contrast: 'Alto contraste',
            low_contrast: 'Contraste reduzido',
            colorblind: 'Amigável a daltônicos',
            grayscale: 'Escala de cinza',
            // Dark_mode: 'Modo escuro',
        };

        colorLabel.textContent = labels[mode] || 'Padrão';

        // Atualiza os indicadores visuais
        colorIndicators.innerHTML = '';
        colorPreferences.color_mode_options
            .filter(m => m !== 'default')
            .forEach(m => {
                const span = document.createElement('span');
                span.classList.add('cycle-indicator');
                if (m === mode) {
                    span.classList.add('active');
                }
                colorIndicators.appendChild(span);
            });

        if (mode !== 'default') {
            colorContainer.classList.add('active');
        } else {
            colorContainer.classList.remove('active');
        }
    }

    /**
     *
     */
    function cycleAccessibility() {
        const currentIndex = preferences.zoom_options.indexOf(preferences.zoom_level);
        const nextIndex = (currentIndex + 1) % preferences.zoom_options.length;
        preferences.zoom_level = preferences.zoom_options[nextIndex];

        Repository.setUserPreference('theme_suap_accessibility_zoom_level', preferences.zoom_level);
        syncPreference('zoom_level', preferences.zoom_level);

        // Atualizar atributo no body
        document.body.setAttribute('data-zoom', preferences.zoom_level);

        renderZoom();
    }

    /**
     *
     */
    function cycleColorMode() {
        const modes = colorPreferences.color_mode_options;
        const currentIndex = modes.indexOf(colorPreferences.color_mode);
        const nextIndex = (currentIndex + 1) % modes.length;
        colorPreferences.color_mode = modes[nextIndex];

        // Salvar no Moodle
        Repository.setUserPreference('theme_suap_accessibility_color_mode', colorPreferences.color_mode);
        syncPreference('color_mode', colorPreferences.color_mode);

        // --- Atualizar classes no <body>
        const allModes = colorPreferences.color_mode_options;
        allModes.forEach(m => {
            document.body.classList.remove(`accessibility_color_mode_${m}`);
        });

        document.body.classList.add(`accessibility_color_mode_${colorPreferences.color_mode}`);

        renderColorMode();
    }

    /**
     *
     */
    function syncInputWithBody() {

        document.querySelectorAll('.custom-checkbox-access input[type="checkbox"]').forEach(input => {
            input.checked = false;
        });

        document.body.classList.forEach(cls => {

            if (cls.startsWith('accessibility_')) {

                const id = cls.replace('accessibility_', '');

                // Marca input booleano
                const checkbox = document.getElementById(id);
                if (checkbox && checkbox.type === "checkbox") {
                    checkbox.checked = true;
                }
            }

        });
    }

    return {
        init: () => {
            syncInputWithBody();

            activeAccessibility();

        }
    };

});
