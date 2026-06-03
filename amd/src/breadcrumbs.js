define(["core/str"], function(str) {

    /**
     *
     */
    function breadcrumbsMobile() {
        const ol = document.querySelector('.breadcrumb');
        // 1) Não faz nada se não houver breadcrumb ou já existir dropdown
        if (!ol || ol.querySelector('.breadcrumb-item.dropdown')) {
 return;
}

        const items = Array.from(ol.querySelectorAll('.breadcrumb-item'));
        if (items.length < 3) {
 return;
} // Precisa ter pelo menos 3 itens

        // 2) Itens que serão “colapsados”
        const hiddenItems = items.slice(1, -1);

        // 3) Cria o <li class="breadcrumb-item dropdown">
        const dropdownLi = document.createElement('li');
        dropdownLi.classList.add('breadcrumb-item', 'dropdown');

        // 4) Cria o botão toggle
        const toggle = document.createElement('button');
        toggle.classList.add('btn', 'p-0');
        toggle.type = 'button';
        toggle.id = 'breadcrumbDropdown';
        toggle.setAttribute('data-toggle', 'dropdown');
        toggle.setAttribute('aria-haspopup', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.textContent = '•••';
        dropdownLi.appendChild(toggle);

        // 5) Cria o <div class="dropdown-menu">
        const menu = document.createElement('div');
        menu.classList.add('dropdown-menu');
        menu.setAttribute('aria-labelledby', 'breadcrumbDropdown');

        // 6) Clona cada link oculto para dentro do dropdown
        hiddenItems.forEach(li => {
            const link = li.querySelector('a')?.cloneNode(true) || document.createElement('span');
            link.classList.add('dropdown-item');
            if (link.tagName === 'SPAN') {
 link.textContent = li.textContent;
}
            menu.appendChild(link);
        });
        dropdownLi.appendChild(menu);

        // 7) Insere o dropdown antes do último item
        ol.insertBefore(dropdownLi, items[items.length - 1]);

    }

    return {
        init: () => {
            breadcrumbsMobile();
        }
    };

});