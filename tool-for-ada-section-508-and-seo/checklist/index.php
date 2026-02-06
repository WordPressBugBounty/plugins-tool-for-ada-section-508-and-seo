<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap checklist">
<div id="save-notice" style="display:none;" class="updated settings-error notice is-dismissible"> 
<p><strong>Click Save button (at bottom) to save your Changes</strong></p>
</div>
    <form action="options.php" method="post">
        <?php
        settings_fields('dvin508-checklist');
        do_settings_sections('dvin508-checklist');

        function dvin508_render_checklist_group($title, $description, $settings) {
            if ($title) echo '<h2>' . esc_html($title) . '</h2>';
            if ($description) echo '<p>' . esc_html($description) . '</p>';
            foreach ($settings as $group) {
                echo '<h3>' . esc_html($group['heading']) . '</h3>';
                foreach ($group['items'] as $key => $val) {
                    $option_value = get_option($key);
                    $checked = $option_value === 'on' ? 'checked="checked"' : '';
                    $active_class = $option_value === 'on' ? 'active-point' : '';
                    echo "<label class='" . esc_attr($active_class) . "'>
                            <input type='checkbox' name='" . esc_attr($key) . "' $checked>
                            <strong>" . esc_html($val) . "</strong>
                        </label>";
                }
            }
        }

        dvin508_render_checklist_group('1. Perceivable', 'Information and user interface components have to be presentable to users in ways that they can perceive. (i.e. a blind person hears the content and a deaf person must see the content)', [
            ['heading' => 'Guideline 1.1: Text Alternatives', 'items' => $settings1],
            ['heading' => 'Guideline 1.2 Alternatives For Multimedia', 'items' => $settings2],
            ['heading' => 'Guideline 1.3 Adaptable Content', 'items' => $settings3],
            ['heading' => 'Guideline 1.4 Distinguishable Content', 'items' => $settings4],
        ]);

        dvin508_render_checklist_group('2. Operable', 'User interface components and navigation must be operable by all users.', [
            ['heading' => 'Guideline 2.1 Keyboard Functionality', 'items' => $settings5],
            ['heading' => 'Guideline 2.2 Adjustable Time Limits', 'items' => $settings6],
            ['heading' => 'Guideline 2.3 Seizures And Physical Reactions', 'items' => $settings7],
            ['heading' => 'Guideline 2.4 Content Navigation', 'items' => $settings8],
            ['heading' => 'Guideline 2.5 Inputs Beyond The Keyboard', 'items' => $settings9],
        ]);

        dvin508_render_checklist_group('3. Understandable', 'Information and the operation of the user interface must be understandable by all users.', [
            ['heading' => 'Guideline 3.1 Readable', 'items' => $settings10],
            ['heading' => 'Guideline 3.2 Predictable', 'items' => $settings11],
            ['heading' => 'Guideline 3.3 Input Assistance', 'items' => $settings12],
        ]);

        dvin508_render_checklist_group('4. Robust', 'Content must be robust enough that it can be interpreted by a wide variety of user agents, including assistive technologies, browsers, and plugins.', [
            ['heading' => 'Guideline 4.1 Compatible', 'items' => $settings13],
        ]);
        
        submit_button();
        ?>
    </form>
    <p>
    This checklist is meant to help you better understand the Section 508 (<a href="https://www.w3.org/TR/WCAG21/#intro">WCAG 2.1</a>) guidelines. Follow this link for the official publication of the <a href="https://www.w3.org/TR/WCAG21/#intro">WCAG 2.1</a> recommendations.
    </p>
</div>