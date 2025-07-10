<?php
class TLDR_Plugin
{
  public function __construct()
  {
    add_action('admin_menu', array($this, 'add_tldr_admin_menu'));
    add_action('admin_init', array($this, 'tldr_register_settings'));
  }

  public function add_tldr_admin_menu()
  {
    add_options_page(
      'TLDR Settings',
      'TLDR Plugin Settings',
      'manage_options',
      'tldr-settings',
      array($this, 'settings_tldr_page_html')
    );
  }

  public function tldr_register_settings()
  {
    register_setting('tldr_group', 'tldr_key', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => ''
    ));

    register_setting('tldr_group', 'tldr_limit', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => '150'
    ));

    register_setting('tldr_group', 'tldr_border_color', array(
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => '#f00'
    ));

    add_settings_section(
      'tldr_section',
      'TLDR Configuration',
      function () {
        echo '<p>Enter your API key below.</p>';
      },
      'tldr-settings'
    );

    add_settings_field('tldr_key', 'API Key', array($this, 'api_key_field_html'), 'tldr-settings', 'tldr_section');
    add_settings_field('tldr_limit', 'Text Limit (in words)', array($this, 'api_limit_field_html'), 'tldr-settings', 'tldr_section');
    add_settings_field('tldr_border_color', 'Left Color', array($this, 'border_color_field_html'), 'tldr-settings', 'tldr_section');
  }

  public function api_key_field_html()
  {
    $value = esc_attr(get_option('tldr_key'));
    ?>
    <div style="position: relative; display: inline-block;">
      <input type="password" id="tldr_key" name="tldr_key" value="<?php echo $value; ?>" class="regular-text"
        style="padding-right: 30px;" />
      <span id="toggle-tldr-key" class="dashicons dashicons-visibility"
        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('tldr_key');
        const toggle = document.getElementById('toggle-tldr-key');

        toggle.addEventListener('click', function () {
          const isHidden = input.type === 'password';
          input.type = isHidden ? 'text' : 'password';
          toggle.classList.toggle('dashicons-visibility');
          toggle.classList.toggle('dashicons-hidden');
        });
      });
    </script>
    <?php
  }

  public function api_limit_field_html()
  {
    $value = esc_attr(get_option('tldr_limit'));
    echo '<input type="text" id="tldr_limit" name="tldr_limit" value="' . $value . '" class="regular-text" />';
  }

  public function border_color_field_html()
  {
    $color = get_option('tldr_border_color', '#ffffff');
    echo '<input type="text" id="tldr_border_color" name="tldr_border_color" value="' . esc_attr($color) . '" class="tldr-color-field" />';
  }

  public function settings_tldr_page_html()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'settings';
    ?>
    <div class="wrap">
      <h1>TLDR Plugin</h1>

      <h2 class="nav-tab-wrapper">
        <a href="?page=tldr-settings&tab=settings"
          class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
        <a href="?page=tldr-settings&tab=documentation"
          class="nav-tab <?php echo $active_tab == 'documentation' ? 'nav-tab-active' : ''; ?>">Docs</a>
      </h2>

      <?php if ($active_tab == 'settings'): ?>
        <form method="post" action="options.php">
          <?php
          settings_fields('tldr_group');
          do_settings_sections('tldr-settings');
          submit_button();
          ?>
        </form>
      <?php elseif ($active_tab == 'documentation'): ?>
        <div class="tldr-docs" style="display: flex; gap: 40px; align-items: flex-start;">
          <div class="tldr-docs-content" style="flex: 1;">
            <h2>🧠 How to Generate Your TLDR API Key</h2>
            <p>To start using the TLDR plugin and summarize your blog posts, follow these simple steps:</p>
            <h3>✅ Step-by-Step Instructions</h3>
            <ol>
              <li>
                <strong>Get Your API Key</strong><br>
                Click the button below to create your API account:<br>
                👉 <a href="https://platform.turboline.ai/signup" target="_blank" rel="noopener noreferrer"><strong>Get API
                    Keys</strong></a>
              </li>
              <li>
                <strong>Activate Your Account</strong><br>
                After signing up, check your email for an activation link. Click the link to activate your account.
              </li>
              <li>
                <strong>Sign In to the TLDR Portal</strong><br>
                Once activated, go back to the portal and sign in:<br>
                👉 <a href="https://platform.turboline.ai/signin" target="_blank" rel="noopener noreferrer"><strong>Sign
                    In</strong></a>
              </li>
              <li>
                <strong>Subscribe to the TLDR Product</strong>
                <ul>
                  <li>Click on <strong>Products</strong> in the top right menu.</li>
                  <li>Then click on <strong>TLDR</strong>.</li>
                </ul>
              </li>
              <li>
                <strong>Name Your Subscription</strong>
                <ul>
                  <li>Enter a name like <em>My WordPress Subscription</em></li>
                  <li>Click <strong>Subscribe</strong>.</li>
                </ul>
              </li>
              <li>
                <strong>Access Your API Keys</strong>
                <ul>
                  <li>After subscribing, your <strong>Primary Key</strong> and <strong>Secondary Key</strong> will appear.
                  </li>
                </ul>
              </li>
              <li>
                <strong>Use Your Primary Key</strong>
                <ul>
                  <li>Copy your <strong>Primary Key</strong>.</li>
                  <li>Go back to your WordPress Admin Panel → <strong>TLDR Plugin Settings</strong>.</li>
                  <li>Paste the key in the <strong>API Key</strong> field.</li>
                  <li>Click <strong>Save Changes</strong>.</li>
                </ul>
              </li>
              <li>
                <strong>Add TLDR to Your Posts</strong>
                <ul>
                  <li>Use the shortcode <code>[turboline_tldr]</code> anywhere in your post to display the TLDR summary.</li>
                  <li>The TLDR summary will appear automatically after publishing or updating the post.</li>
                </ul>
              </li>
            </ol>
            <p>✅ That’s it! You’re now ready to start generating summaries directly from your blog editor.</p>
          </div>

          <div class="tldr-docs-video" style="flex: 1; max-width: 560px;">
            <div style="padding:56.25% 0 20px 0;position:relative;">
              <iframe src="https://player.vimeo.com/video/1099170110?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"
                frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
                style="position:absolute;top:0;left:0;width:100%;height:100%;" title="TL;DR"></iframe>
            </div>
            <script src="https://player.vimeo.com/api/player.js"></script>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <?php
  }
}
