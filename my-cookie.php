<?php
  /*
  Plugin Name: MyCookie GDPR Compliance
  Version: 1.0.6
  Description: Another cookie plugin. 
  Author: Dawid Globalsense
  Text Domain: my-cookie
  Domain Path: /languages
  */

  if (!defined('ABSPATH')) exit;

  if (!class_exists('My_Cookie')):

  class My_Cookie {
    function __construct() {
      add_action( 'plugins_loaded', array($this, 'mycookie_init'), 5 );
    }

    function mycookie_init()
    {
      add_action('wp_enqueue_scripts', array($this, 'assets'));
      add_action('admin_menu', array($this, 'admin_page'));
  
      load_plugin_textdomain('my-cookie', false, basename(dirname(__FILE__)) . '/languages');
  
      add_action('cookies_tab', array($this, 'cookies_tab_action'));
      add_action('cookies_tab_content', array($this, 'cookies_tab_content_action'));
  
      add_action('content_tab', array($this, 'content_tab_action'));
      add_action('content_tab_content', array($this, 'content_tab_content_action'));
  
      add_action('design_tab', array($this, 'design_tab_action'));
      add_action('design_tab_content', array($this, 'design_tab_content_action'));
  
      add_action('wp_head', array($this, 'cookies_header'));
      
      // 1 means popup should not be added to the template
      // It can be still loaded via popup
      if (get_option('load_popup') != 1) {
        add_action('wp_footer', array($this, 'cookies_render_html'));
      }
  
      add_shortcode('my-cookie-popup', array($this, 'cookies_render_html_shortcode'));
      
      add_filter( 'the_content', array($this, 'fix_content' ) );

      add_action( 'admin_notices', array($this, 'mycookie_admin_notice') );

      add_action( 'admin_init', array($this, 'register_settings_tabs' ) );

      $this->default_consent_info = __('Our website uses cookies to ensure you get the best experience.', 'my-cookie');
      $this->default_consent_info_en = 'Our website uses cookies to ensure you get the best experience.';
      $this->default_open_s_txt = __('Open settings', 'my-cookie');
      $this->default_open_s_txt_en = 'Open settings';
      $this->default_ok_i_txt = __('Ok, I Understand', 'my-cookie');
      $this->default_ok_i_txt_en = 'Ok, I Understand';
      $this->default_cookie_s_txt = __('Cookie Settings', 'my-cookie');
      $this->default_cookie_s_txt_en = 'Cookie Settings';
      $this->default_cookie_d_txt = __('Cookie Declaration', 'my-cookie');
      $this->default_cookie_d_txt_en = 'Cookie Declaration';
      $this->default_disable_a_txt = __('Disable All', 'my-cookie');
      $this->default_disable_a_txt_en = 'Disable All';
      $this->default_save_c_txt = __('Save & Close', 'my-cookie');
      $this->default_save_c_txt_en = 'Save & Close';

      $this->default_analytics_cookies_list = '_gat_gtag, _ga, _gid';
      $this->default_analytics_item_description = __('Used to distinguish users and sessions for web statistics purposes.', 'my-cookie');
      $this->default_analytics_item_description_en = 'Used to distinguish users and sessions for web statistics purposes.';

      $this->default_pixel_cookies_list = 'fr';
      $this->default_pixel_item_description = __('Helps in determining efficiency of our advertising policy.', 'my-cookie');
      $this->default_pixel_item_description_en = 'Helps in determining efficiency of our advertising policy.';

      $this->default_youtube_cookies_list = 'APISID, CONSENT, HSID, LOGIN_INFO, PREF, SAPISID, SIDCC, SID, SSID, ST, VISITOR_INFO1_LIVE, YSC';
      $this->default_youtube_item_description = __("Stores various information regarding user's preferences.", 'my-cookie');
      $this->default_youtube_item_description_en = "Stores various information regarding user's preferences.";
      $this->default_youtube_placeholder = __("Please change your cookies preferences to see this video.", 'my-cookie');
      $this->default_youtube_placeholder_en = "Please change your cookies preferences to see this video.";

      $this->default_cookie_declaration_prefix = __('We use cookies to give you the best and most relevant experience. Storing data in cookies also gives us ability to track users movements and to gather demographic information. Using our site means you agree with this and understand that we can share information about your use of this site with our social media and advertising providers.', 'my-cookie');
      $this->default_cookie_declaration_prefix_en = 'We use cookies to give you the best and most relevant experience. Storing data in cookies also gives us ability to track users movements and to gather demographic information. Using our site means you agree with this and understand that we can share information about your use of this site with our social media and advertising providers.';
      $this->default_cookie_declaration_suffix = __('You can manage your cookie preferences in the settings area or you can completely disable cookies in your browser, so no data will be collected.', 'my-cookie');
      $this->default_cookie_declaration_suffix_en = 'You can manage your cookie preferences in the settings area or you can completely disable cookies in your browser, so no data will be collected.';
    }

    function register_settings_tabs() {
      register_setting( 'cookies-settings', 'analytics_toggle' );
      register_setting( 'cookies-settings', 'analytics_cookies_list' );
      register_setting( 'cookies-settings', 'analytics_item_description' );
      register_setting( 'cookies-settings', 'analytics_tracking_code' );
      register_setting( 'cookies-settings', 'pixel_toggle' );
      register_setting( 'cookies-settings', 'pixel_cookies_list' );
      register_setting( 'cookies-settings', 'pixel_item_description' );
      register_setting( 'cookies-settings', 'pixel_tracking_code' );
      register_setting( 'cookies-settings', 'youtube_toggle' );
      register_setting( 'cookies-settings', 'youtube_cookies_list' );
      register_setting( 'cookies-settings', 'youtube_item_description' );
      register_setting( 'cookies-settings', 'youtube_placeholder' );

      register_setting( 'content-settings', 'consent_info' );
      register_setting( 'content-settings', 'open_s_txt' );
      register_setting( 'content-settings', 'ok_i_txt' );
      register_setting( 'content-settings', 'declaration_prefix' );
      register_setting( 'content-settings', 'declaration_suffix' );
      register_setting( 'content-settings', 'cookie_d_txt' );
      register_setting( 'content-settings', 'cookie_s_txt' );
      register_setting( 'content-settings', 'disable_a_txt' );
      register_setting( 'content-settings', 'save_c_txt' );
  
      register_setting( 'design-settings', 'load_popup' );
      register_setting( 'design-settings', 'load_css' );
      register_setting( 'design-settings', 'consent_bg_color' );
      register_setting( 'design-settings', 'consent_txt_color' );
      register_setting( 'design-settings', 'consent_btn_bg_color' );
      register_setting( 'design-settings', 'consent_btn_txt_color' );
      register_setting( 'design-settings', 'open_settings_button_font_size');
      register_setting( 'design-settings', 'i_understand_button_font_size');
      register_setting( 'design-settings', 'i_understand_button_vertical_padding');
      register_setting( 'design-settings', 'i_understand_button_horizontal_padding');
      register_setting( 'design-settings', 'consent_custom_padding_top' );
      register_setting( 'design-settings', 'consent_custom_padding_bottom' );
      register_setting( 'design-settings', 'consent_base_font_size' );
      register_setting( 'design-settings', 'delay_consent' );
      register_setting( 'design-settings', 'delay_consent_vh' );
      register_setting( 'design-settings', 'delay_consent_px' );
      register_setting( 'design-settings', 'delay_consent_s' );
      register_setting( 'design-settings', 'popup_base_font_size' );
      register_setting( 'design-settings', 'cookie_d_btn_bg_color' );
      register_setting( 'design-settings', 'cookie_d_btn_txt_color' );
      register_setting( 'design-settings', 'disable_a_btn_bg_color' );
      register_setting( 'design-settings', 'disable_a_btn_txt_color' );
      register_setting( 'design-settings', 'save_c_btn_bg_color' );
      register_setting( 'design-settings', 'save_c_btn_txt_color' );
    }

    function fix_scripts($html, $script) {
      $fixed_scripts = preg_replace('/(<script\b[^><]*)>/i', '$1 type="plain/text" cookie-type="my-cookie[' . $script . ']">', $html);
      return $fixed_scripts;
    }

    function mycookie_admin_notice() {
      echo '<div class="notice notice-warning is-dismissible">
        <p>Please use: <strong>my-cookie__custom-settings</strong> and <strong>my-cookie__custom-declaration</strong> classes for triggering Cookie Settings and Cookie Declaration popups.</p>
      </div>';
    }

    function cookies_header() {

      $analytics_toggle = get_option('analytics_toggle');
      $analytics_tracking_code = get_option('analytics_tracking_code');
      $pixel_toggle = get_option('pixel_toggle');
      $pixel_tracking_code = get_option('pixel_tracking_code');

      if ($analytics_toggle == true) :
        echo $this->fix_scripts($analytics_tracking_code, 'analytics');
      endif;

      if ($pixel_toggle == true) :
        echo $this->fix_scripts($pixel_tracking_code, 'pixel');
      endif;

    }

    function fix_content($content) {

      $youtube_toggle = get_option('youtube_toggle');
      $youtube_placeholder = get_option('youtube_placeholder');
      if ($youtube_toggle == true):
				$content = preg_replace('/<iframe(.*)src=("|\')(.*(youtu\.be|youtube\.com|youtube-nocookie\.com).*)("|\')>/i', '<iframe$1data-src="$3" cookie-type="my-cookie[youtube]" cookie-placeholder="' . $youtube_placeholder . '" cookie-state="hidden">', $content);
        #$content = preg_replace('/(<iframe(.*) src=("|\')[^("|\')](youtu\.be|youtube\.com|youtube-nocookie\.com)*[^><]*)>/i', '$1 cookie-type="my-cookie[youtube]" cookie-placeholder="' . $youtube_placeholder . '" cookie-state="hidden">', $content);
        #$content = str_replace('src', 'data-src', $content);
      endif;

      return $content;

    }
    
    function cookies_render_html_shortcode() {
      ob_start();
  
      $this->cookies_render_html();
  
      return ob_get_clean();
    }
    
    function cookies_render_html() {
      $analytics_toggle = get_option('analytics_toggle');
      $analytics_cookies_list = get_option('analytics_cookies_list');
      $analytics_item_description = get_option('analytics_item_description');
      $pixel_toggle = get_option('pixel_toggle');
      $pixel_cookies_list = get_option('pixel_cookies_list');
      $pixel_item_description = get_option('pixel_item_description');
      $youtube_toggle = get_option('youtube_toggle');
      $youtube_cookies_list = get_option('youtube_cookies_list');
      $youtube_item_description = get_option('youtube_item_description');

      $consent_info = get_option('consent_info');
      $open_s_txt = get_option('open_s_txt');
      $ok_i_txt = get_option('ok_i_txt');
      $declaration_prefix = get_option('declaration_prefix');
      $declaration_suffix = get_option('declaration_suffix');
      $cookie_s_txt = get_option('cookie_s_txt');
      $cookie_d_txt = get_option('cookie_d_txt');
      $disable_a_txt = get_option('disable_a_txt');
      $save_c_txt = get_option('save_c_txt');

      $consent_bg_color = get_option('consent_bg_color');
      $consent_txt_color = get_option('consent_txt_color');
      $consent_btn_bg_color = get_option('consent_btn_bg_color');
      $consent_btn_txt_color = get_option('consent_btn_txt_color');
      $open_settings_button_font_size = get_option('open_settings_button_font_size');
      $i_understand_button_font_size = get_option('i_understand_button_font_size');
      $i_understand_button_vertical_padding = get_option('i_understand_button_vertical_padding');
      $i_understand_button_horizontal_padding = get_option('i_understand_button_horizontal_padding');
      $consent_custom_padding_top = get_option('consent_custom_padding_top');
      $consent_custom_padding_bottom = get_option('consent_custom_padding_bottom');
      $consent_base_font_size = get_option('consent_base_font_size');
      $popup_base_font_size = get_option('popup_base_font_size');
      $cookie_d_btn_bg_color = get_option('cookie_d_btn_bg_color');
      $cookie_d_btn_txt_color = get_option('cookie_d_btn_txt_color');
      $disable_a_btn_bg_color = get_option('disable_a_btn_bg_color');
      $disable_a_btn_txt_color = get_option('disable_a_btn_txt_color');
      $save_c_btn_bg_color = get_option('save_c_btn_bg_color');
      $save_c_btn_txt_color = get_option('save_c_btn_txt_color');

      $load_css = get_option('load_css'); // 0=> yes 1 => no
    ?>
    
    <?php if ($load_css != 1): ?>
      <style>
        #my-cookie__consent {
          <?php echo ($consent_bg_color) ? 'background-color:' . $consent_bg_color . ';' : ''; ?>
          <?php echo ($consent_base_font_size) ? 'font-size:' . $consent_base_font_size . 'px;' : ''; ?>
        }
        #my-cookie__consent .my-cookie__container {
          <?php echo ($consent_custom_padding_top) ? 'padding-top:' . $consent_custom_padding_top . 'px;' : ''; ?>
          <?php echo ($consent_custom_padding_bottom) ? 'padding-bottom:' . $consent_custom_padding_bottom . 'px;' : ''; ?>
        }
        #my-cookie__consent .my-cookie__container,
        #my-cookie__consent .my-cookie__open-settings {
          <?php echo ($consent_txt_color) ? 'color:' . $consent_txt_color . ';' : ''; ?>
        }
        #my-cookie__consent .my-cookie__open-settings {
          <?php echo ($open_settings_button_font_size) ? 'font-size:' . $open_settings_button_font_size . 'em;' : ''; ?>
        }
        #my-cookie__consent .my-cookie__accept-button {
          <?php echo ($consent_btn_bg_color) ? 'background-color:' . $consent_btn_bg_color . ';' : ''; ?>
          <?php echo ($consent_btn_txt_color) ? 'color:' . $consent_btn_txt_color . ';' : ''; ?>
          <?php echo ($i_understand_button_font_size) ? 'font-size:' . $i_understand_button_font_size . 'em;' : ''; ?>
          <?php echo ($i_understand_button_vertical_padding) ? 'padding-top:' . $i_understand_button_vertical_padding . 'em;' : ''; ?>
          <?php echo ($i_understand_button_vertical_padding) ? 'padding-bottom:' . $i_understand_button_vertical_padding . 'em;' : ''; ?>
          <?php echo ($i_understand_button_horizontal_padding) ? 'padding-left:' . $i_understand_button_horizontal_padding . 'em;' : ''; ?>
          <?php echo ($i_understand_button_horizontal_padding) ? 'padding-right:' . $i_understand_button_horizontal_padding . 'em;' : ''; ?>
        }
        #my-cookie__popup {
          <?php echo ($popup_base_font_size) ? 'font-size:' . $popup_base_font_size . 'px;' : ''; ?>
        }
        .my-cookie__button.blue {
          <?php echo ($cookie_d_btn_bg_color) ? 'background-color:' . $cookie_d_btn_bg_color . ';' : ''; ?>
          <?php echo ($cookie_d_btn_txt_color) ? 'color:' . $cookie_d_btn_txt_color . ';' : ''; ?>
        }
        .my-cookie__button.red {
          <?php echo ($disable_a_btn_bg_color) ? 'background-color:' . $disable_a_btn_bg_color . ';' : ''; ?>
          <?php echo ($disable_a_btn_txt_color) ? 'color:' . $disable_a_btn_txt_color . ';' : ''; ?>
        }
        .my-cookie__button.green {
          <?php echo ($save_c_btn_bg_color) ? 'background-color:' . $save_c_btn_bg_color . ';' : ''; ?>
          <?php echo ($save_c_btn_txt_color) ? 'color:' . $save_c_btn_txt_color . ';' : ''; ?>
        }
      </style>
    <?php endif; ?>

      <div
        id="my-cookie__consent"
        data-delay="<?php echo get_option('delay_consent'); ?>"
        data-delay-px="<?php echo get_option('delay_consent_px'); ?>"
        data-delay-vh="<?php echo get_option('delay_consent_vh'); ?>"
        data-delay-s="<?php echo get_option('delay_consent_s'); ?>"
      >

      <div class="my-cookie__container">
          <div class="my-cookie__desc">
              <?php if (!$consent_info || $consent_info == $this->default_consent_info_en) { echo $this->default_consent_info; } else { echo $consent_info; } ?>
          </div>

          <div class="my-cookie__controls">
            <div
                class="my-cookie__open-settings"
                id="click-toggleSettings"
            >
              <?php if (!$open_s_txt || $open_s_txt == $this->default_open_s_txt_en) { echo $this->default_open_s_txt; } else { echo $open_s_txt; } ?>
            </div>

            <div class="my-cookie__accept-button" id="click-savePreferences">
              <?php if (!$ok_i_txt || $ok_i_txt == $this->default_ok_i_txt_en) { echo $this->default_ok_i_txt; } else { echo $ok_i_txt; } ?>
            </div>
          </div>
      </div>
  </div>

  <div id="my-cookie__popup">
      <div class="my-cookie__wrapper" id="my-cookie__clickable-area">
          <div class="my-cookie__container table-container">
              <div id="my-cookie__declaration-title"><div class="my-cookie__go-back">&times;</div><?php _e('Cookie Declaration', 'my-cookie'); ?></div>
              <div id="my-cookie__declaration">

                <div>
                  <?php if (!$declaration_prefix || $declaration_prefix == $this->default_cookie_declaration_prefix_en) { echo $this->default_cookie_declaration_prefix; } else { echo $declaration_prefix; } ?>
                </div>

                <table>
                  <thead>
                    <tr>
                        <th class="th-name"><?php _e('Service', 'my-cookie'); ?></th>
                        <th class="th-cookies"><?php _e('Cookies', 'my-cookie'); ?></th>
                        <th class="th-desc"><?php _e('Description', 'my-cookie'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($analytics_toggle == true) : ?>
                    <tr>
                        <td class="td-name">Google Analytics</td>
                        <td class="td-cookies"><?php echo ($analytics_cookies_list) ? $analytics_cookies_list : $this->default_analytics_cookies_list; ?></td>
                        <td class="td-desc">
                          <?php if (!$analytics_item_description || $analytics_item_description == $this->default_analytics_item_description_en) { echo $this->default_analytics_item_description; } else { echo $analytics_item_description; } ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($pixel_toggle == true) : ?>
                    <tr>
                        <td class="td-name">Facebook Pixel</td>
                        <td class="td-cookies"><?php echo ($pixel_cookies_list) ? $pixel_cookies_list : $this->default_pixel_cookies_list; ?></td>
                        <td class="td-desc">
                          <?php if (!$pixel_item_description || $pixel_item_description == $this->default_pixel_item_description_en) { echo $this->default_pixel_item_description; } else { echo $pixel_item_description; } ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($youtube_toggle == true) : ?>
                    <tr>
                        <td class="td-name">Youtube</td>
                        <td class="td-cookies"><?php echo ($youtube_cookies_list) ? $youtube_cookies_list : $this->default_youtube_cookies_list; ?></td>
                        <td class="td-desc">
                          <?php if (!$youtube_item_description || $youtube_item_description == $this->default_youtube_item_description_en) { echo $this->default_youtube_item_description; } else { echo $youtube_item_description; } ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>

                <div>
                  <?php if (!$declaration_suffix || $declaration_suffix == $this->default_cookie_declaration_suffix_en) { echo $this->default_cookie_declaration_suffix; } else { echo $declaration_suffix; } ?>
                </div>

              </div>
              <table id="my-cookie__table" cellspacing="0" cellpadding="0">
                <thead>
                  <tr>
                      <th></th>
                      <th class="th-name"><?php _e('Service', 'my-cookie'); ?></th>
                      <th class="th-cookies"><?php _e('Cookies', 'my-cookie'); ?></th>
                      <th class="th-desc"><?php _e('Description', 'my-cookie'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($analytics_toggle == true) : ?>
                  <tr>
                      <td class="td-checkbox">
                          <div class="my-cookie__toggle">
                              <input
                                  type="checkbox"
                                  class="my-cookie__toggle-checkbox"
                                  name="my-cookie[analytics]"
                                  checked="checked"
                              />
                              <span class="my-cookie__toggle-switch"></span>
                              <span class="my-cookie__toggle-track"></span>
                          </div>
                      </td>
                      <td class="td-name">Google Analytics</td>
                      <td class="td-cookies"><?php echo ($analytics_cookies_list) ? $analytics_cookies_list : $this->default_analytics_cookies_list; ?></td>
                      <td class="td-desc">
                        <?php if (!$analytics_item_description || $analytics_item_description == $this->default_analytics_item_description_en) { echo $this->default_analytics_item_description; } else { echo $analytics_item_description; } ?>
                      </td>
                  </tr>
                  <?php endif; ?>
                  <?php if ($pixel_toggle == true) : ?>
                  <tr>
                      <td class="td-checkbox">
                          <div class="my-cookie__toggle">
                              <input
                                  type="checkbox"
                                  class="my-cookie__toggle-checkbox"
                                  name="my-cookie[pixel]"
                                  checked="checked"
                              />
                              <span class="my-cookie__toggle-switch"></span>
                              <span class="my-cookie__toggle-track"></span>
                          </div>
                      </td>
                      <td class="td-name">Facebook Pixel</td>
                      <td class="td-cookies"><?php echo ($pixel_cookies_list) ? $pixel_cookies_list : $this->default_pixel_cookies_list; ?></td>
                      <td class="td-desc">
                          <?php if (!$pixel_item_description || $pixel_item_description == $this->default_pixel_item_description_en) { echo $this->default_pixel_item_description; } else { echo $pixel_item_description; } ?>
                      </td>
                  </tr>
                  <?php endif; ?>
                  <?php if ($youtube_toggle == true) : ?>
                  <tr>
                      <td class="td-checkbox">
                          <div class="my-cookie__toggle">
                              <input
                                  type="checkbox"
                                  class="my-cookie__toggle-checkbox"
                                  name="my-cookie[youtube]"
                                  checked="checked"
                              />
                              <span class="my-cookie__toggle-switch"></span>
                              <span class="my-cookie__toggle-track"></span>
                          </div>
                      </td>
                      <td class="td-name">Youtube</td>
                      <td class="td-cookies"><?php echo ($youtube_cookies_list) ? $youtube_cookies_list : $this->default_youtube_cookies_list; ?></td>
                      <td class="td-desc">
                          <?php if (!$youtube_item_description || $youtube_item_description == $this->default_youtube_item_description_en) { echo $this->default_youtube_item_description; } else { echo $youtube_item_description; } ?>
                      </td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
          </div>
          <div class="my-cookie__container buttons-container" id="my-cookie__buttons-container">
              <div
                  class="my-cookie__button blue"
                  id="my-cookie__cookie_settings"
              >
                <?php if (!$cookie_s_txt || $cookie_s_txt == $this->default_cookie_s_txt_en) { echo $this->default_cookie_s_txt; } else { echo $cookie_s_txt; } ?>
              </div>
              <div
                  class="my-cookie__button blue"
                  id="my-cookie__cookie_declaration"
              >
                <?php if (!$cookie_d_txt || $cookie_d_txt == $this->default_cookie_d_txt_en) { echo $this->default_cookie_d_txt; } else { echo $cookie_d_txt; } ?>
              </div>
              <div
                  class="my-cookie__button red"
                  id="my-cookie__turn_off_all"
              >
                <?php if (!$disable_a_txt || $disable_a_txt == $this->default_disable_a_txt_en) { echo $this->default_disable_a_txt; } else { echo $disable_a_txt; } ?>
              </div>
              <div
                  class="my-cookie__button green"
                  id="my-cookie__save_close"
              >
                <?php if (!$save_c_txt || $save_c_txt == $this->default_save_c_txt_en) { echo $this->default_save_c_txt; } else { echo $save_c_txt; } ?>
              </div>
          </div>
      </div>
  </div>
    <?php
    }

    function assets() {
      wp_enqueue_script('my_cookie_main_js', plugins_url('assets/frontend.min.js', __FILE__), array(), '1.0.6', true);
      wp_enqueue_style('my_cookie_main_css', plugins_url('assets/frontend.min.css', __FILE__) );
    }

    function admin_assets() {
      $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html'));
      wp_localize_script('jquery', 'cm_settings', $cm_settings);

      wp_enqueue_script('wp-theme-plugin-editor');
      wp_enqueue_style('wp-codemirror');

      wp_enqueue_style( 'wp-color-picker' );

      wp_enqueue_script('my_cookie_admin_js', plugins_url('assets/admin.min.js', __FILE__), array('wp-color-picker', 'wp-theme-plugin-editor'), false, true);
      wp_enqueue_style('my_cookie_admin_css', plugins_url('assets/admin.min.css', __FILE__));
    }

    function admin_con_assets() {
      add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
    }

    function admin_page() {
      $cookie_page = add_menu_page(
        'My Cookie',
        'My Cookie',
        'manage_options',
        'my_cookie',
        array($this, 'admin_menu_page'),
        'dashicons-image-filter',
        71
      );

      add_action('load-' . $cookie_page, array($this, 'admin_con_assets'));
    }

    function admin_menu_page() {

      global $active_tab;
      $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'cookies'; ?>

      <h2 class="nav-tab-wrapper">
      <?php
        do_action( 'cookies_tab' );
        do_action( 'design_tab' );
        do_action( 'content_tab' );
      ?>
      </h2>
      <?php
        do_action( 'cookies_tab_content' );
        do_action( 'design_tab_content' );
        do_action( 'content_tab_content' );
      ?>
      <?php
    }

    function cookies_tab_action() {
        global $active_tab; ?>
        <a class="nav-tab <?php echo $active_tab == 'cookies' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=my_cookie&tab=cookies' ); ?>"><?php _e('Cookies settings', 'my-cookie'); ?></a>
        <?php
    }

    function cookies_tab_content_action() {
        global $active_tab;
        if ($active_tab == 'cookies' || '') {
          ?>
          <form method="post" action="options.php">
            <?php settings_fields( 'cookies-settings' ); ?>
            <?php do_settings_sections( 'cookies-settings' ); ?>
            <div class="my-cookie_settings-group">
              <p><?php _e('Preset Items', 'my-cookie'); ?></p>
              <div class="my-cookie_settings-table" id="my-cookie_scripts-table">
                <table cellspacing="0" cellpadding="0">
                  <thead>
                    <tr>
                      <th></th>
                      <th><?php _e('Service', 'my-cookie'); ?></th>
                      <th><p class="has-excerpt"><span><?php _e('Cookies List', 'my-cookie'); ?></span><span><?php _e('Cookies this service creates and uses', 'my-cookie'); ?></span></p></th>
                      <th><p class="has-excerpt"><span><?php _e('Item Description', 'my-cookie'); ?></span><span><?php _e('Service description (what it does)', 'my-cookie'); ?></span></p></th>
                      <th><p class="has-excerpt"><span><?php _e('Tracking Code(s)', 'my-cookie'); ?></span><span><?php _e('Paste your tracking codes here (including the script tag)', 'my-cookie'); ?></span></p></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="my-cookie__toggle">
                            <input
                                type="checkbox"
                                class="my-cookie__toggle-checkbox"
                                name="analytics_toggle"
                                value="on"
                                <?php echo '' . ( get_option('analytics_toggle') === 'on' ? 'checked="checked"' : '' ) . ''; ?>
                            />
                            <span class="my-cookie__toggle-switch"></span>
                            <span class="my-cookie__toggle-track"></span>
                        </div>
                      </td>
                      <td>Google Analytics</td>
                      <td><textarea name="analytics_cookies_list" data-default="<?php echo $this->default_analytics_cookies_list; ?>"><?php echo get_option('analytics_cookies_list') ? get_option('analytics_cookies_list') : $this->default_analytics_cookies_list; ?></textarea></td>
                      <td><textarea name="analytics_item_description" data-default="<?php echo $this->default_analytics_item_description; ?>"><?php echo get_option('analytics_item_description') ? get_option('analytics_item_description'): $this->default_analytics_item_description; ?></textarea></td>
                      <td><textarea name="analytics_tracking_code" class="my-cookie_editor"><?php echo get_option('analytics_tracking_code'); ?></textarea></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="my-cookie__toggle">
                            <input
                                type="checkbox"
                                class="my-cookie__toggle-checkbox"
                                name="pixel_toggle"
                                value="on"
                                <?php echo '' . ( get_option('pixel_toggle') === 'on' ? 'checked="checked"' : '' ) . ''; ?>
                            />
                            <span class="my-cookie__toggle-switch"></span>
                            <span class="my-cookie__toggle-track"></span>
                        </div>
                      </td>
                      <td>Facebook Pixel</td>
                      <td><textarea name="pixel_cookies_list" data-default="<?php echo $this->default_pixel_cookies_list; ?>"><?php echo get_option('pixel_cookies_list') ? get_option('pixel_cookies_list') : $this->default_pixel_cookies_list; ?></textarea></td>
                      <td><textarea name="pixel_item_description" data-default="<?php echo $this->default_pixel_item_description; ?>"><?php echo get_option('pixel_item_description') ? get_option('pixel_item_description') : $this->default_pixel_item_description; ?></textarea></td>
                      <td><textarea name="pixel_tracking_code" class="my-cookie_editor"><?php echo get_option('pixel_tracking_code'); ?></textarea></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="my-cookie__toggle">
                            <input
                                type="checkbox"
                                class="my-cookie__toggle-checkbox"
                                name="youtube_toggle"
                                value="on"
                                <?php echo '' . ( get_option('youtube_toggle') === 'on' ? 'checked="checked"' : '' ) . ''; ?>
                            />
                            <span class="my-cookie__toggle-switch"></span>
                            <span class="my-cookie__toggle-track"></span>
                        </div>
                      </td>
                      <td>Youtube Embed</td>
                      <td><textarea name="youtube_cookies_list" data-default="<?php echo $this->default_youtube_cookies_list; ?>"><?php echo get_option('youtube_cookies_list') ? get_option('youtube_cookies_list') : $this->default_youtube_cookies_list; ?></textarea></td>
                      <td><textarea name="youtube_item_description" data-default="<?php echo $this->default_youtube_item_description; ?>"><?php echo get_option('youtube_item_description') ? get_option('youtube_item_description'): $this->default_youtube_item_description; ?></textarea></td>
                      <td><textarea name="youtube_placeholder" data-default="<?php echo $this->default_youtube_placeholder; ?>"><?php echo get_option('youtube_placeholder') ? get_option('youtube_placeholder'): $this->default_youtube_placeholder; ?></textarea></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="my-cookie_buttons-area">
              <?php submit_button(); ?>
              <div class="button-secondary" id="my-cookie_scripts-reset" data-value="'<?php _e('Click on save changes to confirm', 'my-cookie'); ?>'"><?php _e('Restore Default', 'my-cookie'); ?></div>
            </div>
          </form>
          <?php
        }
    }

    function content_tab_action() {
        global $active_tab; ?>
        <a class="nav-tab <?php echo $active_tab == 'content' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=my_cookie&tab=content' ); ?>"><?php _e('Content settings', 'my-cookie'); ?></a>
        <?php
    }

    function content_tab_content_action() {
        global $active_tab;
        if ($active_tab == 'content') {
          ?>
          <form method="post" action="options.php">
            <?php settings_fields( 'content-settings' ); ?>
            <?php do_settings_sections( 'content-settings' ); ?>
            <div class="my-cookie_settings-group">
              <p><?php _e('Consent', 'my-cookie'); ?></p>
              <div class="my-cookie_settings-table">
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('Cookie consent info', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('The information text on the consent', 'my-cookie'); ?></span>
                    </p>
                    <textarea name="consent_info" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_consent_info; ?>"><?php echo get_option('consent_info') ? get_option('consent_info') : $this->default_consent_info; ?></textarea>
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Open Settings" button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('Edit label for the "Open Settings" button', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" name="open_s_txt" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_open_s_txt; ?>" value="<?php echo get_option('open_s_txt') ? get_option('open_s_txt'): $this->default_open_s_txt; ?>">
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Ok, I Understand" button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('Edit label for the "Ok, I Understand" button', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" name="ok_i_txt" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_ok_i_txt; ?>" value="<?php echo get_option('ok_i_txt') ? get_option('ok_i_txt') : $this->default_ok_i_txt; ?>">
                </div>
              </div>
            </div>
            <div class="my-cookie_settings-group">
              <p><?php _e('Cookie Declaration', 'my-cookie'); ?></p>
              <div class="my-cookie_settings-table">
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('Cookie declaration prefix', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('This text will be displayed at the top of the cookie declaration table', 'my-cookie'); ?></span>
                    </p>
                    <textarea name="declaration_prefix" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_cookie_declaration_prefix; ?>"><?php echo get_option('declaration_prefix') ? get_option('declaration_prefix') : $this->default_cookie_declaration_prefix; ?></textarea>
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('Cookie declaration suffix', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('This text will be displayed at the bottom of the cookie declaration table', 'my-cookie'); ?></span>
                    </p>
                    <textarea name="declaration_suffix" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_cookie_declaration_suffix; ?>"><?php echo get_option('declaration_suffix') ? get_option('declaration_suffix') : $this->default_cookie_declaration_suffix; ?></textarea>
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Cookie Settings" Button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('This button will appear when Cookie Declaration is present', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_cookie_s_txt; ?>" name="cookie_s_txt" value="<?php echo get_option('cookie_s_txt') ? get_option('cookie_s_txt') : $this->default_cookie_s_txt; ?>">
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Cookie Declaration" Button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('Edit label for the "Cookie Declaration" button', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_cookie_d_txt; ?>" name="cookie_d_txt" value="<?php echo get_option('cookie_d_txt') ? get_option('cookie_d_txt') : $this->default_cookie_d_txt; ?>">
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Disable All" Button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('Edit label for the "Disable All" button', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_disable_a_txt; ?>" name="disable_a_txt" value="<?php echo get_option('disable_a_txt') ? get_option('disable_a_txt') : $this->default_disable_a_txt; ?>">
                </div>
                <div>
                    <p class="has-excerpt">
                      <span><?php _e('"Save & Close" Button', 'my-cookie'); ?></span>
                      <span class="my-cookie_excerpt"><?php _e('Edit label for the "Save & Close" button', 'my-cookie'); ?></span>
                    </p>
                    <input type="text" class="my-cookie_content-settings-input" data-default="<?php echo $this->default_save_c_txt; ?>" name="save_c_txt" value="<?php echo get_option('save_c_txt') ? get_option('save_c_txt') : $this->default_save_c_txt; ?>">
                </div>
              </div>
            </div>
            <div class="my-cookie_buttons-area">
              <?php submit_button(); ?>
              <div class="button-secondary" id="my-cookie_content-reset" data-value="<?php _e('Click on save changes to confirm', 'my-cookie'); ?>"><?php _e('Restore Default', 'my-cookie'); ?></div>
            </div>
          </form>
          <?php
        }
    }

    function design_tab_action() {
        global $active_tab; ?>
        <a class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=my_cookie&tab=design' ); ?>"><?php _e('Design settings', 'my-cookie'); ?></a>
        <?php
    }

    function design_tab_content_action() {
        global $active_tab;
        if ($active_tab == 'design') {
          ?>
          <form method="post" action="options.php">
            <?php settings_fields( 'design-settings' ); ?>
            <?php do_settings_sections( 'design-settings' ); ?>
            <div class="my-cookie_settings-group">
                <p><?php _e('Display Settings', 'my-cookie'); ?></p>
                <div class="my-cookie_settings-table" >
                    <div>
                        <p><?php _e('Automatically add popup to template', 'my-cookie'); ?></p>
                        <select id="load_popup" name="load_popup">
                            <option value="0"><?php _e('Yes', 'my-cookie'); ?></option>
                            <option value="1" <?php if (get_option('load_popup') == 1) { echo 'selected="selected"'; } ?>><?php _e('No, I will load popup myself via shortcode [my-cookie-popup]', 'my-cookie'); ?></option>
                        </select>
                    </div>
                    <div>
                        <p><?php _e('Automatically load custom css (inline css generated by the plugin)', 'my-cookie'); ?></p>
                        <select id="load_css" name="load_css">
                            <option value="0"><?php _e('Yes', 'my-cookie'); ?></option>
                            <option value="1" <?php if (get_option('load_css') == 1) { echo 'selected="selected"'; } ?>><?php _e('I do not need custom css', 'my-cookie'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-cookie_settings-group">
              <p><?php _e('Consent', 'my-cookie'); ?></p>
              <div class="my-cookie_settings-table" >
                <div>
                  <p><?php _e('Background color', 'my-cookie'); ?></p>
                  <input type="text" name="consent_bg_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('consent_bg_color'); ?>">
                </div>
                <div>
                  <p><?php _e('Text color', 'my-cookie'); ?></p>
                  <input type="text" name="consent_txt_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('consent_txt_color'); ?>">
                </div>
                <div>
                  <p><?php _e('Buttons background color', 'my-cookie'); ?></p>
                  <input type="text" name="consent_btn_bg_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('consent_btn_bg_color'); ?>">
                </div>
                <div>
                  <p><?php _e('Buttons text color', 'my-cookie'); ?></p>
                  <input type="text" name="consent_btn_txt_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('consent_btn_txt_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Open settings" button font size (em)', 'my-cookie'); ?></p>
                  <input type="text" name="open_settings_button_font_size" class="my-cookie_design-settings-input" placeholder="1.5" value="<?php echo get_option('open_settings_button_font_size'); ?>">
                </div>
                <div>
                  <p><?php _e('"I understand" button font size (em)', 'my-cookie'); ?></p>
                  <input type="text" name="i_understand_button_font_size" class="my-cookie_design-settings-input" placeholder="1.4" value="<?php echo get_option('i_understand_button_font_size'); ?>">
                </div>
                <div>
                  <p><?php _e('"I understand" button vertical padding (em)', 'my-cookie'); ?></p>
                  <input type="text" name="i_understand_button_vertical_padding" class="my-cookie_design-settings-input" placeholder="1.3" value="<?php echo get_option('i_understand_button_vertical_padding'); ?>">
                </div>
                <div>
                  <p><?php _e('"I understand" button horizontal padding (em)', 'my-cookie'); ?></p>
                  <input type="text" name="i_understand_button_horizontal_padding" class="my-cookie_design-settings-input" placeholder="1.8" value="<?php echo get_option('i_understand_button_horizontal_padding'); ?>">
                </div>
                <div>
                  <p><?php _e('Padding top (px)', 'my-cookie'); ?></p>
                  <input type="text" name="consent_custom_padding_top" class="my-cookie_design-settings-input" placeholder="25" value="<?php echo get_option('consent_custom_padding_top'); ?>">
                </div>
                <div>
                  <p><?php _e('Padding bottom (px)', 'my-cookie'); ?></p>
                  <input type="text" name="consent_custom_padding_bottom" class="my-cookie_design-settings-input" placeholder="25" value="<?php echo get_option('consent_custom_padding_bottom'); ?>">
                </div>
                <div>
                  <p><?php _e('Base font size (px)', 'my-cookie'); ?></p>
                  <input type="text" name="consent_base_font_size" class="my-cookie_design-settings-input" placeholder="16" value="<?php echo get_option('consent_base_font_size'); ?>">
                </div>
                <div>
                  <p><?php _e('Delay consent', 'my-cookie'); ?></p>
                  <select id="delay_consent" name="delay_consent">
                    <option value="0"><?php _e('No, consent appears immediately', 'my-cookie'); ?></option>
                    <option value="1" <?php if (get_option('delay_consent') == 1) { echo 'selected="selected"'; } ?>><?php _e('By a certain amount of px user has scrolled down', 'my-cookie'); ?></option>
                    <option value="2" <?php if (get_option('delay_consent') == 2) { echo 'selected="selected"'; } ?>><?php _e('By a certain amount of vh user has scrolled down', 'my-cookie'); ?></option>
                    <option value="3" <?php if (get_option('delay_consent') == 3) { echo 'selected="selected"'; } ?>><?php _e('By a certain amount of time (s)', 'my-cookie'); ?></option>
                  </select>
                </div>
                <div id="delay_consent_px" class="delay_consent <?php if (get_option('delay_consent') == 1) { echo 'active'; } ?>" data-value="1">
                  <p><?php _e('Value in pixels', 'my-cookie'); ?></p>
                  <input type="text" name="delay_consent_px" class="my-cookie_design-settings-input" placeholder="500" value="<?php echo get_option('delay_consent_px'); ?>">
                </div>
                <div id="delay_consent_vh" class="delay_consent <?php if (get_option('delay_consent') == 2) { echo 'active'; } ?>" data-value="2">
                  <p><?php _e('Value in vh', 'my-cookie'); ?></p>
                  <input type="text" name="delay_consent_vh" class="my-cookie_design-settings-input" placeholder="50" value="<?php echo get_option('delay_consent_vh'); ?>">
                </div>
                <div id="delay_consent_s" class="delay_consent <?php if (get_option('delay_consent') == 3) { echo 'active'; } ?>" data-value="3">
                  <p><?php _e('Value in seconds', 'my-cookie'); ?></p>
                  <input type="text" name="delay_consent_s" class="my-cookie_design-settings-input" placeholder="3" value="<?php echo get_option('delay_consent_s'); ?>">
                </div>
              </div>
            </div>
            <div class="my-cookie_settings-group">
              <p><?php _e('Popup', 'my-cookie'); ?></p>
              <div class="my-cookie_settings-table">
                <div>
                  <p><?php _e('Base font size (px)', 'my-cookie'); ?></p>
                  <input type="text" name="popup_base_font_size" class="my-cookie_design-settings-input" placeholder="16" value="<?php echo get_option('popup_base_font_size'); ?>">
                </div>
                <div>
                  <p><?php _e('"Cookie Declaration" button background color', 'my-cookie'); ?></p>
                  <input type="text" name="cookie_d_btn_bg_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('cookie_d_btn_bg_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Cookie Declaration" button text color', 'my-cookie'); ?></p>
                  <input type="text" name="cookie_d_btn_txt_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('cookie_d_btn_txt_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Disable All" button background color', 'my-cookie'); ?></p>
                  <input type="text" name="disable_a_btn_bg_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('disable_a_btn_bg_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Disable All" button text color', 'my-cookie'); ?></p>
                  <input type="text" name="disable_a_btn_txt_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('disable_a_btn_txt_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Save & Close" button background color', 'my-cookie'); ?></p>
                  <input type="text" name="save_c_btn_bg_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('save_c_btn_bg_color'); ?>">
                </div>
                <div>
                  <p><?php _e('"Save & Close" button text color', 'my-cookie'); ?></p>
                  <input type="text" name="save_c_btn_txt_color" class="my-cookie_colorpicker my-cookie_design-settings-input" value="<?php echo get_option('save_c_btn_txt_color'); ?>">
                </div>
              </div>
            </div>
            <div class="my-cookie_buttons-area">
              <?php submit_button(); ?>
              <div class="button-secondary" id="my-cookie_design-reset" data-value="<?php _e('Click on save changes to confirm', 'my-cookie'); ?>"><?php _e('Restore Default', 'my-cookie'); ?></div>
            </div>
          </form>
          <?php
        }
    }


  }

  endif;

$My_Cookie = new My_Cookie();

?>
