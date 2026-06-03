define(["core/str", "jquery"], function(str, $) {

    /**
     *
     */
    function toggleUserProfile() {
        const collapseElement = document.getElementById("collapseProfile");
        const toggleButton = document.querySelector(".toggle-section[data-toggle='collapse']");

        if (collapseElement && toggleButton) {
            $('#collapseProfile').on('show.bs.collapse', function() {
                toggleButton.textContent = "Ver menos";
            });

            $('#collapseProfile').on('hide.bs.collapse', function() {
                toggleButton.textContent = "Ver mais";
            });
        }
    }

    /**
     *
     */
    function dropdownAccordion() {
        const container = document.querySelector('.dropdown-accordion');
        const btn = container.querySelector('.dropdown-btn');
        const dropdown = container.querySelector('.dropdown-menu');

        // Abre/fecha o menu ao clicar no botão
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const aberto = dropdown.classList.toggle('show');
            btn.setAttribute('aria-expanded', aberto);
        });

        // Fecha o menu ao clicar fora
        document.addEventListener('click', () => {
            dropdown.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
            dropdown.querySelectorAll('.menu > li.open')
            .forEach(openItem => {
                openItem.classList.remove('open');
            });
        });

        // Accordion interno
        dropdown.querySelectorAll('.menu > li').forEach(item => {
            const submenu = item.querySelector('.submenu');
            if (!submenu) {
 return;
}

            item.addEventListener('click', e => {
            e.stopPropagation(); // Não fecha o dropdown
            // fecha os outros
            dropdown.querySelectorAll('.menu > li.open')
                .forEach(openItem => {
                    if (openItem !== item) {
 openItem.classList.remove('open');
}
                });
            // Alterna o clicado
            item.classList.toggle('open');
            });
        });
    }

    return {
        init: () => {
            toggleUserProfile();
            dropdownAccordion();
        }
    };
});