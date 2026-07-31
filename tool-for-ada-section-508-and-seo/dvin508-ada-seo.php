<?php
/**
 * Plugin Name: Accessibility Tools & Alt Text Finder 
 * Description: A multi-tool for WordPress developers that helps you improve your websites accessibility and Section 508 compliance.
 * Version: 3.0
 * Author: Joseph LoPreste, ClearPath Web Accessibility
 * Author URI: https://508accessible.com/
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: accessibility-tools-alt-text-finder
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'DVIN508_VERSION', '3.0' );
define( 'DVIN508_PLUGIN_FILE', __FILE__ );
define( 'DVIN508_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DVIN508_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check minimum PHP version
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    add_action( 'admin_notices', 'dvin508_php_version_notice' );
    return;
}

/**
 * Display PHP version notice
 */
function dvin508_php_version_notice() {
    echo '<div class="notice notice-error"><p>';
    printf(
        /* translators: 1: Current PHP version, 2: Required PHP version */
        esc_html__( 'Accessibility Tools & Alt Text Finder requires PHP %2$s or higher. You are running PHP %1$s.', 'accessibility-tools-alt-text-finder' ),
        PHP_VERSION,
        '7.4'
    );
    echo '</p></div>';
}

// Initialize plugin only in admin area
if ( is_admin() ) {
    new Dvin508_seo_Wp_List_Table();
}

// Include required files
require_once DVIN508_PLUGIN_DIR . 'dvin508-media-api.php';
require_once DVIN508_PLUGIN_DIR . 'dvin508-post-api.php';

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Dvin508_seo_Wp_List_Table {
    /**
     * Constructor will create the menu item
     */

    public $icon = ""; 
    
    public $active_tab;

    private $settings1 = [
        'a111' => '[Level A] 1.1.1 - Provide text alternatives (Alt Text) for images and other non-text content, including user interface components.',
    ];
    
    private $settings2 = [
        'a122' => '[Level A] 1.2.2/1.2.4 - Provide synchronized captioning for ALL videos and multimedia content.',
        'a123' => '[Level A] 1.2.3/1.2.5 - Provide synchronized audio description for ALL videos and multimedia content.',
    ];

    private $settings3 = [
        'a131' => '[Level A] 1.3.1 - Make sure the information, structure, and relationships conveyed visually are also available to users of assistive technology.',
        'a132' => '[Level A] 1.3.2 - Provide a reasonable and logical reading order when using assistive technology.',
        'a133' => '[Level A] 1.3.3 - Make sure that instructions are not conveyed only through sound, shape, size, or visual orientation.',
        'a134' => '[Level AA] 1.3.4 - Make sure the content does not restrict its view or operation to a single display orientation, such as portrait or landscape unless a specific display orientation is essential.',
        'a135' => '[Level AA] 1.3.5 -  Make sure to identify the purpose of an input field in forms or any data collection.',
    ];

    private $settings4 = [
        'a141' => '[Level A] 1.4.1 - Make sure that information, prompts or instructions are not conveyed only through color.',
        'a142' => '[Level A] 1.4.2 - There has to be a way to stop, pause, mute, or adjust the volume to the audio that plays automatically.',
        'a143' => '[Level AA] 1.4.3 - Meet the minimum specified contrast ratio between the background and the foreground of text and images. [3:1 for links - or - 4.5:1 for everything else]',
        'a144' => '[Level AA] 1.4.4 - Make sure the text is still readable and functional even if the font is resized to 200 percent.',
        'a145' => '[Level AA] 1.4.5 - Use actual text and do not use images of text.',
    ];

    private $settings5 = [
        'a211' => '[Level A] 2.1.1 - There must be full functionality when using only the keyboard interface.',
        'a212' => '[Level A] 2.1.2 - Make sure that the keyboard focus is not trapped when the keyboard is used for navigation.',
    ];

    private $settings6 = [
        'a221' => '[Level A] 2.2.1 - Provide flexible or adjustable time limits.',
        'a222' => '[Level A] 2.2.2 - Give user control over moving, blinking, scrolling, or information that updates automatically.',
    ];
    
    private $settings7 = [
        'a231' => '[Level A] 2.3.1 Make sure nothing flashes more than three times per second unless the flash is below the general red flash threshold.',
    ];
        
    private $settings8 = [
        'a241' => ' [Level A] 2.4.1 - Must have a skip navigation link or other means to bypass repetitive content.',
        'a242' => '[Level A] 2.4.2 - Provide descriptive and informative page titles.',
        'a243' => '[Level A] 2.4.3 - Provide a keyboard-oriented navigation order that is reasonable and logical.',
        'a244' => '[Level A] 2.4.4 - Make sure that all of your links are descriptive. Ie. do not use “Click Here” as your link description.',
        'a245' => '[Level AA] 2.4.5 - Include at least 2 or more ways to locate a web page within a set of web pages.',
        'a246' => '[Level AA] 2.4.6 - Make the headings and labels descriptive.',
        'a247' => '[Level AA] 2.4.7 - Make sure the keyboard focus is visually apparent when somebody uses the keyboard to navigate.',
    ];
    
    private $settings9 = [
        'a251' => '[Level A] 2.5.1 - Make functions that use multipoint or path-based gestures for operation can be operated with a single pointer without a path-based gesture unless it is essential.',
        'a252' => '[Level A] 2.5.2 - You have to be able to cancel or reverse an action taken ',
        'a253' => '[Level A] 2.5.3 - User interface components with labels that include text or images, the name must include the text that is presented visually.',
        'a254' => '[Level A] 2.5.4 - Make sure that functions operated by device/user motion can also be disabled and operated by device/user interface components unless its essential.',
    ];

    private $settings10 = [
        'a311' => '[Level A] 3.1.1 - Make sure that the default language of your content is exposed to assistive technology.',
        'a312' => '[Level AA] 3.1.2 - Make sure that all the changes in language are exposed to assistive technology.',
    ];
    
    private $settings11 = [
        'a321' => '[Level A] 3.2.1 - Make sure that user interface components do not initiate a change of context when receiving focus. Ie. when the mouse scrolls over something.',
        'a322' => '[Level A] 3.2.2 - When changing the settings of the user interface components, it does not automatically cause a change of context.',
        'a323' => '[Level AA] 3.2.3 - Make sure that repeated navigational components happen in the same relative order each time they are encountered.',
        'a324' => '[Level AA] 3.2.4 - Make sure that the components having the same functionality are identified consistently.',
    ];

    private $settings12 = [
        'a331' => '[Level A] 3.3.1 - Make sure automatically detected input errors are identified and described in the text to the user.',
        'a332' => '[Level A] 3.3.2 - Make sure you have labels or instructions when content requires user input.',
        'a333' => '[Level AA] 3.3.3 - Make sure the system creates and displays suggestions for correction when input errors are automatically detected unless it jeopardizes the security.',
        'a334' => '[Level AA] 3.3.4 - When legal, financial, or test data can be changed or deleted the changes or deletions can be reversed, verified, or confirmed.',
    ];
        
    private $settings13 = [
        'a411' => '[Level A] 4.1.1 - Make sure your website or software is parsed into a single data structure, making sure elements are nested properly and any IDs are unique.',
        'a412' => '[Level A] 4.1.2 - All of the user interface components names, roles and values can be programmed and notifications of the changes available to the user agents like assistive technology.',
        'a413' => '[Level AA] 4.1.3 - Create status messages that can be presented to the user by assistive technologies without being the focus.',
    ];

    public function __construct() {
        // Sanitize and validate tab parameter
        $this->active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'images';
        
        // Validate tab against allowed values
        $allowed_tabs = array( 'images', 'color', 'checklist', 'aa-toolbox', 'resources', 'wcag-course', 'signup', 'upgrade' );
        if ( ! in_array( $this->active_tab, $allowed_tabs, true ) ) {
            $this->active_tab = 'images';
        }
        
         add_action( 'admin_menu', array( $this, 'add_menu_example_list_table_page' ) );
         add_action( 'admin_init', array( $this, 'checklist_options' ) );
         add_action( 'admin_notices', array( $this, 'plugin_notice' ) );
         add_action( 'admin_init', array( $this, 'plugin_notice_dismissed' ) );
    }

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_example_list_table_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $menu = add_submenu_page(
            'tools.php',
            __( 'Accessibility Multi-Tool', 'accessibility-tools-alt-text-finder' ),
            __( 'Accessibility Multi-Tool', 'accessibility-tools-alt-text-finder' ),
            'manage_options',
            'pitheme-seo',
            array( $this, 'list_table_page' )
        );
        
        // Enqueue the appropriate CSS/JS based on the active tab
        switch ( $this->active_tab ) {
            case 'images':
                add_action( 'load-' . $menu, array( $this, 'css_js_enque' ) );
                break;
            case 'color':
                add_action( 'load-' . $menu, array( $this, 'css_js_color' ) );
                break;
            case 'checklist':
                add_action( 'load-' . $menu, array( $this, 'css_js_checklist' ) );
                break;
            default:
                add_action( 'load-' . $menu, array( $this, 'css_js_toolbox_resource' ) );
                break;
        }
    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'accessibility-tools-alt-text-finder' ) );
        }
        ?>
        <div class="wrap" style="padding-top:20px;">
            <h1><?php esc_html_e( 'Accessibility Tools', 'accessibility-tools-alt-text-finder' ); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'images', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'images' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Image Optimization', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'color', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'color' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Contrast Checker', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'checklist', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'checklist' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'ADA Checklist', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'aa-toolbox', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'aa-toolbox' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Accessibility Audit', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'resources', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'resources' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Resources', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'wcag-course', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'wcag-course' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'WCAG Course', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'signup', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'signup' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Contact Us', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'upgrade', admin_url( 'tools.php?page=pitheme-seo' ) ) ); ?>" 
                   class="nav-tab <?php echo 'upgrade' === $this->active_tab ? 'nav-tab-active' : ''; ?>">
                    <strong><?php esc_html_e( 'Upgrade to Pro', 'accessibility-tools-alt-text-finder' ); ?></strong>
                </a>
            </h2>
            <?php
            switch ( $this->active_tab ) {
                case 'images':
                    $this->images();
                    break;
                case 'color':
                    $this->color();
                    break;
                case 'checklist':
                    $this->checklist();
                    break;
                case 'aa-toolbox':
                    $this->toolbox();
                    break;
                case 'resources':
                    $this->resources();
                    break;
                case 'wcag-course':
                    $this->wcag_course();
                    break;
                case 'signup':
                    $this->signup();
                    break;
                case 'upgrade':
                    $this->upgrade();
                    break;
            }
            ?>
        </div>
        <?php
    }

    private function signup() {
        require_once DVIN508_PLUGIN_DIR . 'signup/index.html';
    }
        
    private function upgrade() {
        require_once DVIN508_PLUGIN_DIR . 'upgrade/index.html';
    }    

    private function toolbox() {
        require_once DVIN508_PLUGIN_DIR . 'toolbox/index.php';
    }

    private function resources() {
        require_once DVIN508_PLUGIN_DIR . 'resources/index.html';
    }

    private function wcag_course() {
        ?>
        <div class="wcag-course-page" style="max-width: 900px; margin: 20px 0;">
            <div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                
                <!-- Header Section -->
                <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #22227c;">
                    <a href="https://508accessible.com/contact-us/" target="_blank"><img src="<?php echo DVIN508_PLUGIN_URL . 'img/clearpath-web-accessibility.png'; ?>" alt="WCAG Accessibility Course" style="width: 100%; max-width: 300px; margin-bottom: 20px;"></a>
                    <h1 style="color: #22227c; font-size: 32px; font-weight: 600; margin-bottom: 10px;">
                        Free WCAG Accessibility Course
                    </h1>
                    <p style="font-size: 18px; color: #000; margin: 0;">
                        Learn the fundamentals of web accessibility with our interactive Module 1
                    </p>
                    <p style="font-size: 16px; line-height: 1.6; color: #000; margin-bottom: 15px;">
                        Our free course introduces you to the essential principles of web accessibility and WCAG (Web Content Accessibility Guidelines). 
                        You'll learn practical techniques to make your websites more accessible to everyone, including people with disabilities.
                    </p>
                </div>

                <!-- Course Description -->
                <div style="margin-bottom: 30px;">
                    <h2 style="color: #333; font-size: 24px; margin-bottom: 15px; text-align: center;">
                        What You'll Learn
                    </h2>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #22227c; margin: 20px 0;">
                        <h3 style="color: #000; font-size: 18px; margin-top: 0; margin-bottom: 10px;">
                            Free Version Includes:
                        </h3>
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.8; color: #000;">
                            <li><strong>Module 1:</strong> Introduction to Web Accessibility</li>
                            <li><strong>Quiz 1:</strong> Test your knowledge with 10 questions</li>
                            <li><strong>Certificate:</strong> Earn a completion certificate for Module 1</li>
                            <li><strong>No Login Required:</strong> Start learning immediately</li>
                        </ul>
                    </div>

                    <div style="background: #e8f4f8; padding: 20px; border-left: 4px solid #0073aa; margin: 20px 0;">
                        <h3 style="color: #000; font-size: 18px; margin-top: 0; margin-bottom: 10px;">
                            Upgrade to Pro for:
                        </h3>
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.8; color: #000;">
                            <li><strong>6 Complete Modules</strong> covering all WCAG guidelines</li>
                            <li><strong>6 Comprehensive Quizzes</strong> to test your mastery</li>
                            <li><strong>User Progress Tracking</strong> with secure login</li>
                            <li><strong>Professional Certificates</strong> for each module</li>
                            <li><strong>Admin Dashboard</strong> to manage team progress</li>
                            <li><strong>Email Notifications</strong> for progress updates</li>
                        </ul>
                        
                        <!-- Upgrade CTA -->
                        <div style="text-align: center; padding: 20px;">
                            <p style="font-size: 16px; color: #000; margin-bottom: 15px;">
                                Want access to the complete course with all features?
                            </p>
                            <a href="https://508accessible.com/pricing/" 
                            target="_blank"
                            style="display: inline-block; background: #22227c; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-size: 16px; font-weight: bold; transition: all 0.3s;"
                            onmouseover="this.style.background='#1a1a60';"
                            onmouseout="this.style.background='#22227c';">
                                Upgrade to Pro Version
                            </a>
                        </div>
                    </div>
                    
                </div>

                <!-- Info Section -->
                <div style="background: #fffbea; padding: 20px; border-left: 4px solid #f0b429; margin-bottom: 20px;">
                    <h3 style="color: #000; font-size: 18px; margin-top: 0; margin-bottom: 10px;">
                        How It Works
                    </h3>
                    <ol style="margin: 0; padding-left: 20px; line-height: 1.8; color: #000;">
                        <li>Click the "View Course Page" button above</li>
                        <li>Read through the course outline and click "Start Course"</li>
                        <li>Study Module 1 content at your own pace</li>
                        <li>Take Quiz 1 to test your knowledge (10 random questions)</li>
                        <li>Get your completion certificate!</li>
                        <li>Upgrade to Pro to unlock all 6 modules</li>
                    </ol>
                </div>

                <!-- Course Link Section -->
                <div style="background: linear-gradient(135deg, #22227c 0%, #1a1a60 100%); padding: 30px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <h2 style="color: white; font-size: 24px; margin-top: 0; margin-bottom: 15px;">
                        Ready to Start Learning?
                    </h2>
                    <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin-bottom: 20px;">
                        Access the free course on your website's frontend
                    </p>
                    
                    <?php
                    // Get the course page URL dynamically
                    $course_page = get_page_by_path('accessibility-course');
                    if ($course_page) {
                        $course_url = get_permalink($course_page->ID);
                    } else {
                        $course_url = home_url() . '/accessibility-course/';
                    }
                    ?>
                    
                    <a href="<?php echo esc_url($course_url); ?>" 
                       target="_blank"
                       style="display: inline-block; background: white; color: #22227c; padding: 15px 40px; border-radius: 5px; text-decoration: none; font-size: 18px; font-weight: bold; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.2);"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)';">
                        View Course Page
                    </a>
                </div>

            </div>
        </div>
        <?php
    }

    


    public function plugin_notice() {
        $user_id = get_current_user_id();
        $notice = get_user_meta( $user_id, 'plugin_notice_dismissed', true );
        if ( ! $notice ) {
            ?>
            <div class="updated notice is-dismissible">
                <div style="position:relative">
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'plugin-dismissed', '1' ), 'plugin_notice_dismissed' ) ); ?>" class="notice-dismiss"></a>
                    <h3><?php esc_html_e( 'Thanks so much for downloading our ADA plugin!', 'accessibility-tools-alt-text-finder' ); ?></h3>
                    <p><?php esc_html_e( 'Do you think you could please do us a HUGE favor and give it a 5-star rating on WordPress? It helps us to spread the word and means a lot to us.', 'accessibility-tools-alt-text-finder' ); ?></p>
                    <p>
                        <a class="button button-primary" href="https://wordpress.org/support/plugin/tool-for-ada-section-508-and-seo/reviews/#new-post" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Yes, you guys deserve it', 'accessibility-tools-alt-text-finder' ); ?>
                        </a>
                    </p>
                </div>
            </div>
            <?php
        }
    }
    
    public function plugin_notice_dismissed() {
        if ( isset( $_GET['plugin-dismissed'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'plugin_notice_dismissed' ) ) {
            $user_id = get_current_user_id();
            add_user_meta( $user_id, 'plugin_notice_dismissed', true, true );
            wp_safe_redirect( esc_url( admin_url( 'tools.php?page=pitheme-seo' ) ) );
            exit;
        }
    }
    
    public function css_js_enque() {
        wp_enqueue_script( 'dvin508-inline', DVIN508_PLUGIN_URL . 'seo/dist/inline.bundle.js', array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-polyfills', DVIN508_PLUGIN_URL . 'seo/dist/polyfills.bundle.js', array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-styles-bundle', DVIN508_PLUGIN_URL . 'seo/dist/styles.bundle.js', array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-vendor', DVIN508_PLUGIN_URL . 'seo/dist/vendor.bundle.js', array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-main', DVIN508_PLUGIN_URL . 'seo/dist/main.bundle.js', array(), DVIN508_VERSION, true );
        wp_enqueue_style( 'dvin508-core-css', DVIN508_PLUGIN_URL . 'css/style.css', array(), DVIN508_VERSION );
        $this->enqueue_inline_config_script();
    }

    public function css_js_color() {
        wp_enqueue_style( 'dvin508-contrast-style', DVIN508_PLUGIN_URL . 'contrast/style.css', array(), DVIN508_VERSION );
        wp_enqueue_script( 'dvin508-incrementable', 'https://leaverou.github.com/incrementable/incrementable.js', array(), null, true );
        wp_enqueue_script( 'dvin508-color', DVIN508_PLUGIN_URL . 'contrast/color.js', array( 'dvin508-incrementable' ), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-jscolor', DVIN508_PLUGIN_URL . 'contrast/jscolor.js', array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-contrast', DVIN508_PLUGIN_URL . 'contrast/contrast-ratio.js', array(), DVIN508_VERSION, true );
    }

    public function css_js_checklist() {
        wp_enqueue_style( 'dvin508-checklist-style', DVIN508_PLUGIN_URL . 'checklist/style.css', array(), DVIN508_VERSION );
        wp_enqueue_script( 'dvin508-checklist', DVIN508_PLUGIN_URL . 'checklist/script.js', array(), DVIN508_VERSION, true );
    }

    public function css_js_toolbox_resource() {
        wp_enqueue_style( 'dvin508-toolbox-style', DVIN508_PLUGIN_URL . 'checklist/style.css', array(), DVIN508_VERSION );
    }


    public function enqueue_inline_config_script() {
        wp_register_script( 'dvin508-config', false, array(), DVIN508_VERSION, true );
        wp_enqueue_script( 'dvin508-config' );

        $config_data = array(
            'baseurl'   => get_site_url(),
            'post_type' => array( 'post', 'page' ),
            'nones'     => wp_create_nonce( 'wp_rest' ),
        );

        $inline_script = 'window.config = ' . wp_json_encode( $config_data ) . ';';
        wp_add_inline_script( 'dvin508-config', $inline_script );
    }
    
    public function images() {
        ?>
        <script>
            window.config = {
              baseurl: "<?php echo get_site_url(); ?>",
              post_type: ['post','page'],
              nones: "<?php echo wp_create_nonce( 'wp_rest' ); ?>"
            };
        </script>
        <app-root></app-root>
        <?php
    }
    
    public function color() {
        include DVIN508_PLUGIN_DIR . 'contrast/index.html'; 
    }

    public function checklist() {
        $settings1 = $this->settings1;
        $settings2 = $this->settings2;
        $settings3 = $this->settings3;
        $settings4 = $this->settings4;
        $settings5 = $this->settings5;
        $settings6 = $this->settings6;
        $settings7 = $this->settings7;
        $settings8 = $this->settings8;
        $settings9 = $this->settings9;
        $settings10 = $this->settings10;
        $settings11 = $this->settings11;
        $settings12 = $this->settings12;
        $settings13 = $this->settings13;
        include DVIN508_PLUGIN_DIR . 'checklist/index.php';
    }

    public function checklist_options() {
        // Toolbox setting
        register_setting( 'dvin508-toolbox', 'dvin508_enable_front', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'off',
        ));        

        // Iterate over each settings group and register them
        $settings_groups = array(
            $this->settings1,
            $this->settings2,
            $this->settings3,
            $this->settings4,
            $this->settings5,
            $this->settings6,
            $this->settings7,
            $this->settings8,
            $this->settings9,
            $this->settings10,
            $this->settings11,
            $this->settings12,
            $this->settings13
        );

        foreach ( $settings_groups as $settings ) {
            foreach ( $settings as $key => $val ) {
                register_setting( 'dvin508-checklist', $key, array(
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => '',
                ));
            }
        }
    }
}

add_action( 'wp_enqueue_scripts', 'dvin508_css_js_toolbox' );
function dvin508_css_js_toolbox() {
    $enable_toolbox = get_option( 'dvin508_enable_front', 'off' );
    if ( 'on' === $enable_toolbox && current_user_can( 'administrator' ) ) {
        wp_enqueue_script( 'dvin508-toolbox', DVIN508_PLUGIN_URL . 'toolbox/js/tota11y.min.js', array( 'jquery' ), DVIN508_VERSION, true );
    }
}

// Login function removed - no authentication needed

// Registration functions removed - no authentication needed
add_action('wp_ajax_load_course_content', 'load_course_content');
add_action('wp_ajax_nopriv_load_course_content', 'load_course_content');
add_action('wp_ajax_test_course_ajax', 'test_course_ajax');
add_action('wp_ajax_nopriv_test_course_ajax', 'test_course_ajax');
// User management AJAX handlers removed - free plugin doesn't track user progress

function load_course_content() {
    // Load the course content from the admin version
    ob_start();
    require_once plugin_dir_path(__FILE__) . 'courses/index.php';
    $content = ob_get_clean();
    
    wp_send_json_success($content);
}

function test_course_ajax() {
    global $wpdb;
    $table = dvin508_courses_table();
    
    // Test if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    
    // If table doesn't exist, try to create it
    if (!$table_exists) {
        error_log('Course table does not exist, attempting to create it...');
        dvin508_courses_install();
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    }
    
    // Get table structure if it exists
    $table_structure = null;
    if ($table_exists) {
        $table_structure = $wpdb->get_results("DESCRIBE $table");
    }
    
    wp_send_json_success([
        'message' => 'AJAX is working',
        'table_name' => $table,
        'table_exists' => $table_exists,
        'table_structure' => $table_structure,
        'wp_debug' => defined('WP_DEBUG') && WP_DEBUG,
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}

// Add shortcode for frontend course
add_shortcode('accessibility_course', 'accessibility_course_shortcode');
if (!function_exists('accessibility_course_shortcode')) {
    function accessibility_course_shortcode($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'courses/frontend-course.php';
        return ob_get_clean();
    }
}

// AJAX handler for loading course content
add_action('wp_ajax_load_course_content', 'load_course_content');
add_action('wp_ajax_nopriv_load_course_content', 'load_course_content');
if (!function_exists('load_course_content')) {
    function load_course_content() {
        if (!wp_verify_nonce($_POST['nonce'], 'dvin508_courses_nonce')) {
            wp_send_json_error(['message' => 'Security check failed.']);
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'courses/index.php';
        $content = ob_get_clean();
        
        wp_send_json_success(['content' => $content]);
    }
}

// Registration function removed - no authentication needed

register_activation_hook(__FILE__, 'dvin508_courses_install');
function dvin508_courses_install(){
	global $wpdb;
	$table = $wpdb->prefix . 'dvin508_course_users';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		username varchar(60) NOT NULL,
		email varchar(100) NOT NULL,
		pass_hash varchar(255) NOT NULL,
		progress varchar(50) DEFAULT 'module1',
		last_login datetime NULL,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY username (username),
		UNIQUE KEY email (email)
	) {$charset_collate};";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
	
	// Ensure columns exist for existing tables
	$columns = $wpdb->get_results("SHOW COLUMNS FROM {$table}");
	$column_names = array_column($columns, 'Field');
	
	if (!in_array('progress', $column_names)) {
		$wpdb->query("ALTER TABLE {$table} ADD COLUMN progress varchar(50) DEFAULT 'module1'");
		error_log('Added progress column to existing table');
	}
	
	if (!in_array('last_login', $column_names)) {
		$wpdb->query("ALTER TABLE {$table} ADD COLUMN last_login datetime NULL");
		error_log('Added last_login column to existing table');
	}
}

// Database table functions removed - free plugin doesn't use database

// User progress functions removed - free plugin doesn't track user progress

// All user management functions removed - free plugin doesn't track user progress

/**
 * Create a dedicated course page
 */
if (!function_exists('create_course_page')) {
    function create_course_page() {
	// Check if the page already exists
	$existing_page = get_page_by_path('accessibility-course');
	
	if ($existing_page) {
		// Update existing page to use shortcode
		wp_update_post(array(
			'ID' => $existing_page->ID,
			'post_content' => '[accessibility_course]'
		));
		return;
	}
	
	// Create the course page with shortcode content
	$page_data = array(
		'post_title'   => 'Accessibility Course',
		'post_name'    => 'accessibility-course',
		'post_content' => '[accessibility_course]',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1
	);
	
	$page_id = wp_insert_post($page_data);
	
	if ($page_id && !is_wp_error($page_id)) {
		// Add a note for admins
		update_post_meta($page_id, '_course_page_note', 'This page was automatically created by the Accessibility Tools plugin for the course system.');
	}
    }
}

// Flush rewrite rules on activation to register our routes
register_activation_hook(__FILE__, function(){
	dvin508_courses_install();
	create_course_page();
	flush_rewrite_rules();
});

// Also flush on deactivation to clean up
register_deactivation_hook(__FILE__, function(){
	flush_rewrite_rules();
});