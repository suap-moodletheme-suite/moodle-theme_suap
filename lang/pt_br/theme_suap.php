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

// A description shown in the admin theme selector.
$string['choosereadme'] = 'O tema SUAP é um tema filho do tema Boost';
// The name of our plugin.
$string['pluginname'] = 'SUAP';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Direita';
// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Configurações avançadas';
// The brand colour setting.
$string['brandcolor'] = 'Cor da marca';
// The brand colour setting description.
$string['brandcolor_desc'] = 'Cor de destaque';
// A description shown in the admin theme selector.
$string['configtitle'] = 'Configurações do tema SUAP';
// Name of the first settings tab.
$string['generalsettings'] = 'Configurações gerais';
// Preset files setting.
$string['presetfiles'] = 'Arquivos adicionais de predefinição de tema';
// Preset files help text.
$string['presetfiles_desc'] = 'Arquivos predefinidos podem ser usados ​​para alterar drasticamente a aparência do tema. Consulte <a href=https://docs.moodle.org/dev/Boost_Presets>Predefinições de Boost</a> para obter informações sobre como criar e compartilhar seus próprios arquivos predefinidos e consulte <a href=http://moodle.net/boost>Repositório de predefinições</a> para predefinições que outras pessoas compartilharam.';
// Preset setting.
$string['preset'] = 'Tema predefinido';
// Preset help text.
$string['preset_desc'] = 'Escolha uma predefinição para alterar amplamente a aparência do tema';
// Raw SCSS setting.
$string['rawscss'] = 'SCSS bruto';
// Raw SCSS setting help text.
$string['rawscss_desc'] = 'Use este campo para fornecer o código SCSS ou CSS que será injetado no final do style sheet';
// Raw initial SCSS setting.
$string['rawscsspre'] = 'SCSS inicial bruto';
// Raw initial SCSS setting help text.
$string['rawscsspre_desc'] = 'Neste campo você pode fornecer o código SCSS de inicialização, ele será injetado antes de tudo. Na maioria das vezes você usará esta configuração para definir variáveis';

// Drawers aditional strings
$string['drawer_course_index'] = "Índice da disciplina";
$string['drawer_blocks'] = "Gaveta de Blocos";
$string['drawer_user'] = "Menu do usuário";
$string['allconversations'] = "todas";
$string['unreadmessages'] = "Não lidas";
$string['user_preference_menu'] = "Menu na parte inferior";

$string['accessibility'] = "Acessibilidade";
$string['dyslexia_friendly'] = "Fonte amigável a disléxicos";
$string['align_left'] = "Alinhar texto à esquerda";
$string['highlight_links'] = "Destacar links";
$string['stop_animations'] = "Parar animações";
$string['hide_illustrative_images'] = "Ocultar imagens ilustrativas";
$string['increase_cursor_size'] = "Cursor do mouse grande";
$string['enable_vlibras'] = "Habilitar VLibras";
$string['high_line_height'] = "Linhas mais distantes";

// Frontpage aditional strings
$string['workload'] = 'Carga horária';
$string['certificate'] = 'Certificado';
$string['pt-br'] = 'Português';
$string['es'] = 'Espanhol';
$string['upto_hours'] = 'Até {$a} horas';

// frontpage-settings.php
$string['frontpagesettings'] = 'Configurações da página inicial';
$string['frontpage_title'] = 'Título da página inicial';
$string['frontpage_title_desc'] = '';
$string['frontpage_buttons_configtextarea'] = 'Configuração dos botões da página inicial';
$string['frontpage_buttons_configtextarea_desc'] = 'Apague o trecho (/n) e pressione "Enter" para aplicar a quebra de linha';
$string['frontpage_button_home'] = 'Início';
$string['frontpage_button_about'] = 'Sobre';

$string['pagination_secret'] = 'Segredo de paginação';
$string['pagination_secret_desc'] = 'É necessário criar um token na seção de web services do Moodle para dispositivos móveis';

$string['frontpage_main_courses_title'] = 'Título da seção de cursos da página inicial';
$string['frontpage_main_courses_title_desc'] = '';
$string['frontpage_buttons_configtextarea_when_user_logged'] = 'Configuração dos botões da página inicial quando o usuário está logado';
$string['frontpage_buttons_configtextarea_when_user_logged_desc'] = 'Apague o trecho (/n) e pressione "Enter" para aplicar a quebra de linha';
$string['frontpage_button_courses'] = 'Cursos';
$string['frontpage_button_courses_desc'] = '';
$string['frontpage_button_learningpaths'] = 'Trilhas';
$string['frontpage_button_learningpaths_desc'] = '';

// Configurações do rodapé.
$string['footer_title'] = 'Título do rodapé';
$string['footer_title_desc'] = 'Título principal exibido no rodapé.';

$string['footer_support_button'] = 'Botão de suporte';
$string['footer_support_button_desc'] = 'Texto do botão de suporte exibido no rodapé.';
$string['footer_support_button_url'] = 'Link para o botão de suporte';
$string['footer_support_button_url_desc'] = '';

$string['footer_social_media_text'] = 'Texto das redes sociais';
$string['footer_social_media_text_desc'] = 'Texto sobre as redes sociais do IFRN ZL no rodapé.';
$string['footer_social_media_facebook'] = 'Facebook URL';
$string['footer_social_media_facebook_desc'] = '';
$string['footer_social_media_instagram'] = 'Instagram URL';
$string['footer_social_media_instagram_desc'] = '';
$string['footer_social_media_youtube'] = 'Youtube URL';
$string['footer_social_media_youtube_desc'] = '';

// Mapa do rodapé
$string['footer_map_list'] = 'Listas de links do rodapé';
$string['footer_map_list_desc'] = '';

// Créditos do rodapé.
$string['footer_credits_text'] = 'Texto dos créditos';
$string['footer_credits_text_desc'] = 'Texto exibido nos créditos do rodapé.';

$string['footer_credits_first_link'] = 'Primeiro link dos créditos';
$string['footer_credits_first_link_desc'] = '';
$string['footer_credits_first_link_url'] = 'URL do primeiro link dos créditos';
$string['footer_credits_first_link_url_desc'] = '';
$string['footer_credits_first_link_new_window'] = 'Abrir nova aba no primeiro link';
$string['footer_credits_first_link_new_window_desc'] = '';

$string['footer_credits_second_link'] = 'Segundo link dos créditos';
$string['footer_credits_second_link_desc'] = '';
$string['footer_credits_second_link_url'] = 'URL do segundo link dos créditos';
$string['footer_credits_second_link_url_desc'] = '';
$string['footer_credits_second_link_new_window'] = 'Abrir nova aba no segundo link';
$string['footer_credits_second_link_new_window_desc'] = '';

// Incourse aditional strings
$string['contentbutton'] = 'Conteúdo';

// Profile aditional strings
$string['aboutme'] = 'Sobre mim';
$string['certificates'] = 'Certificados';
$string['describe_yourself'] = 'Se descreva para a sua comunidade';
$string['no_your_certificates'] = 'Conclua algum curso para obter certificados';
$string['no_your_badges'] = 'Explore os nossos cursos para obter emblemas';
$string['no_description'] = 'Sem descrição por enquanto';
$string['no_certificates'] = 'Sem certificados';
$string['no_badges'] = 'Nenhum emblema para ser exibido';

// Enrolment aditional strings
$string['issue_certificate'] = 'Emitir certificado';
$string['login'] = 'Realizar login';
$string['no_description_course'] = 'Sem descrição do curso por enquanto';
$string['overview'] = 'Visão geral';
$string['instructor'] = 'Docente';
$string['instructors'] = 'Docentes';
$string['comments'] = 'Comentários';
$string['no_description_instructor'] = 'Sem descrição do docente';

// Setting layout navigation menu
$string['layouttype'] = 'Sempre mostrar o menu superior';
$string['layouttype_desc'] = 'O menu superior é usado nos Moodles que não estão integrados ao Painel AVA';
