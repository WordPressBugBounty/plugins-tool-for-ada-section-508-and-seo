<?php
/**
 * Frontend Course Page Template
 * This template displays the accessibility course for frontend users
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get plugin directory URL
$plugin_url = plugin_dir_url(__FILE__);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accessibility Course - <?php bloginfo('name'); ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .course-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .course-header {
            background: #22227c;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .course-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5em;
            font-weight: 500;
        }
        .course-header p {
            margin: 0;
            font-size: 1.2em;
            opacity: 0.9;
        }
        .hidden {
            display: none !important;
        }
        .loading {
            text-align: center;
            padding: 40px;
        }
        .loading h2 {
            color: #22227c;
            margin-bottom: 10px;
        }
        .loading p {
            color: #666;
        }
        .aa-course-btn {
            background: #22227c !important;
            color: white !important;
            border: none !important;
            padding: 15px 30px !important;
            border-radius: 8px !important;
            font-size: 18px !important;
            cursor: pointer !important;
            display: inline-block !important;
            margin: 20px 0 !important;
            text-decoration: none !important;
            box-shadow: 10px 10px 10px #d4d3d3 !important;
            font-weight: bold !important;
        }
        .aa-course-btn:hover {
            background: #1a1a60 !important;
            color: white !important;
        }
        .aa-course-btn-secondary {
            background: #22227c !important;
            color: white !important;
            border: none !important;
            box-shadow: 10px 10px 10px #d4d3d3 !important;
            margin: 0 10px 0 0 !important;
        }
        .aa-course-btn-secondary:hover {
            background: #22227c !important;
            color: white !important;
            box-shadow: 10px 10px 10px #d4d3d3 !important;
        }
        .aa-quiz-question {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .aa-quiz-options label {
            display: block;
            margin: 10px 0;
            cursor: pointer;
        }
        .aa-quiz-results {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .aa-quiz-pass {
            color: #28a745;
            font-weight: bold;
        }
        .aa-quiz-fail {
            color: #dc3545;
            font-weight: bold;
        }
        .module-content {
            padding: 40px;
            line-height: 1.8;
        }
        .module-content h1 {
            color: #000;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #22227c;
        }
        .module-content h2 {
            color: #000;
            margin: 30px 0 15px 0;
            font-size: 1.4em;
        }
        .module-content h3 {
            color: #000;
            margin: 20px 0 10px 0;
            font-size: 1.2em;
        }
        .module-content p {
            margin: 15px 0;
            color: #000;
        }
        .module-content ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        .module-content li {
            margin: 8px 0;
            color: #000;
        }
        .quiz-content {
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .quiz-content h1 {
            color: #000;
            margin-bottom: 30px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #22227c;
        }
        .quiz-question {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .quiz-question h3 {
            color: #000;
            margin-bottom: 20px;
            font-size: 1.1em;
            font-weight: 600;
            line-height: 1.5;
        }
        .quiz-option {
            display: block;
            margin: 12px 0;
            padding: 10px 15px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #000;
        }
        .quiz-option:hover {
            background: #f0f8ff;
            border-color: #22227c;
        }
        .quiz-option input[type="radio"] {
            margin-right: 10px;
        }
        .quiz-option label {
            cursor: pointer;
            margin: 0;
            display: block;
        }
        .quiz-submit {
            text-align: center;
            margin: 40px 0;
        }
        .quiz-results {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }
        .quiz-results h3 {
            color: #000;
            margin-bottom: 20px;
        }
        .quiz-score {
            font-size: 1.2em;
            margin: 15px 0;
        }
        .congratulations-content {
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }
        .congratulations-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 20px;
            background: #22227c;
            color: white;
            border-radius: 15px;
        }
        .congratulations-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .congratulations-header h1 {
            font-size: 2.5em;
            margin: 0 0 15px 0;
            color: white;
        }
        .congratulations-header h2 {
            font-size: 1.5em;
            margin: 0 0 15px 0;
            color: white;
            opacity: 0.9;
        }
        .congratulations-subtitle {
            font-size: 1.1em;
            margin: 0;
            opacity: 0.8;
        }
        .upgrade-section {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
        }
        .upgrade-section h3 {
            color: #000;
            font-size: 1.8em;
            margin-bottom: 15px;
        }
        .upgrade-section p {
            color: #000;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        .pro-benefits {
            margin: 40px 0;
        }
        .pro-benefits h4 {
            color: #000;
            font-size: 1.4em;
            margin-bottom: 25px;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .benefit-item {
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .benefit-item:hover {
            transform: translateY(-5px);
        }
        .benefit-icon {
            font-size: 2em;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .benefit-text {
            text-align: left;
        }
        .benefit-text strong {
            display: block;
            color: #000;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .benefit-text span {
            color: #000;
            font-size: 0.9em;
        }
        .upgrade-cta {
            margin-top: 40px;
        }
        .upgrade-btn {
            display: inline-flex;
            align-items: center;
            background: #ff6b35;
            color: white;
            padding: 18px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
            text-decoration: none;
        }
        .btn-text {
            margin-right: 10px;
        }
        .btn-arrow {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }
        .upgrade-btn:hover .btn-arrow {
            transform: translateX(5px);
        }
        .upgrade-note {
            margin-top: 35px;
            color: #000;
            font-style: italic;
        }
        #course-content {
            padding: 40px;
        }
        .course-outline {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 30px;
        }
        .course-outline h2 {
            color: #000;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .course-modules {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .module-card {
            position: relative;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px 50px 20px 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .module-card h5 {
            color: #22227c;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .module-card p {
            color: #000;
            margin: 0;
        }
        .aa-quiz-next-actions {
            text-align: center;
        }
        .quiz-question.quiz-correct {
            border-color: #28a745;
            box-shadow: 0 0 0 1px rgba(40,167,69,0.15);
        }
        .quiz-question.quiz-incorrect {
            border-color: #dc3545;
            box-shadow: 0 0 0 1px rgba(220,53,69,0.15);
        }
        .quiz-feedback {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
            font-size: 0.95em;
            color: #000;
        }
        .quiz-feedback-correct {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .quiz-feedback-incorrect {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .quiz-feedback-explanation {
            margin-top: 4px;
        }
    </style>
    <link rel="stylesheet" href="<?php echo $plugin_url; ?>courses.css">
</head>
<body>
    <div class="course-container">
        <div class="course-header">
            <a href="https://508accessible.com/contact-us/" target="_blank"><img src="<?php echo DVIN508_PLUGIN_URL . 'img/cpwa-white-logo.png'; ?>" alt="Accessibility Course" style="width: 100%; max-width: 300px; margin-bottom: 20px;"></a>
            <h1><b>Accessibility Course</b></h1>
            <p>Learn about web accessibility and Section 508 compliance</p>
        </div>
        
        <div id="course-content">
            <div id="course-outline" class="course-outline">
                <h2>Course Overview</h2>
                <p>This comprehensive course covers all aspects of web accessibility, from foundational principles to advanced implementation techniques. You'll learn how to create inclusive digital experiences that work for everyone.</p>
                
                <div class="course-modules">
                    <div class="module-card">
                        <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px; box-shadow: 4px 4px 10px #d4d3d3;">FREE</span>
                        <h5>Module 1: Foundations</h5>
                        <p>Learn the basics of digital accessibility, disability types, and legal requirements.</p>
                    </div>
                    <div class="module-card">
                        <span style="background: #3B82F6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px;">PRO</span>
                        <h5>Module 2: WCAG 2.2 Deep Dive</h5>
                        <p>Master the Web Content Accessibility Guidelines and success criteria.</p>
                    </div>
                    <div class="module-card">
                        <span style="background: #3B82F6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px;">PRO</span>
                        <h5>Module 3: Design Principles</h5>
                        <p>Apply accessibility principles to visual design, typography, and layout.</p>
                    </div>
                    <div class="module-card">
                        <span style="background: #3B82F6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px;">PRO</span>
                        <h5>Module 4: Development</h5>
                        <p>Implement accessible code using semantic HTML and ARIA.</p>
                    </div>
                    <div class="module-card">
                        <span style="background: #3B82F6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px;">PRO</span>
                        <h5>Module 5: Testing & Remediation</h5>
                        <p>Test for accessibility issues and create remediation plans.</p>
                    </div>
                    <div class="module-card">
                        <span style="background: #3B82F6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; position: absolute; right: 10px; top: 10px;">PRO</span>
                        <h5>Module 6: Agency Implementation</h5>
                        <p>Integrate accessibility into project workflows and client communication.</p>
                    </div>
                </div>
                
                <div style="text-align: center; margin: 40px 0 0;">
                    <button id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 15px 30px; font-weight: bold; border-radius: 8px; font-size: 18px; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3;">
                        Start Course
                    </button>
                    <a href="https://508accessible.com/pricing/" target="_blank" id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 18px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3; text-decoration: none;">
                        Upgrade To Pro Version
                    </a>
                    <p style="margin-top: 15px; color: #000;">
                        <strong>Free Version:</strong> Access to Module 1 and Quiz 1<br>
                        <strong>Pro Version:</strong> Complete course with all 6 modules, user management, and certificates
                    </p>
                </div>
            </div>


            <!-- <div id="course-outline" class="course-outline">
                <div style="padding: 40px;">
                    <h2>Web Accessibility &amp; WCAG 2.2 Compliance for Agencies</h2>
                    <p><strong>Course Objective:</strong> Equip agency teams with the knowledge and tools to integrate accessibility best practices into their workflows, ensuring WCAG 2.2 Level A and AA compliance across client projects.</p>
                    
                    <h3>Course Modules:</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 30px 0;">
                        <div style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9;">
                            <h4>Module 1: Foundations of Web Accessibility</h4>
                            <ul>
                                <li>What is digital accessibility and why it matters</li>
                                <li>Overview of disabilities and assistive technologies</li>
                                <li>Legal and business drivers (ADA, Section 508, AODA, etc.)</li>
                                <li>Introduction to WCAG 2.2 principles (POUR)</li>
                            </ul>
                            <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">FREE</span>
                        </div>
                        
                    </div>
                    
                    <div style="text-align: center; margin: 40px 0;">
                        <button id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 15px 30px; font-weight: bold; border-radius: 8px; font-size: 18px; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3;">
                            Start Course
                        </button>
                        <a href="https://508accessible.com/pricing/" target="_blank" id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 18px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3; text-decoration: none;">
                            Upgrade To Pro Version
                        </a>
                        <p style="margin-top: 15px; color: #000;">
                            <strong>Free Version:</strong> Access to Module 1 and Quiz 1<br>
                            <strong>Pro Version:</strong> Complete course with all 6 modules, user management, and certificates
                        </p>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Set up WordPress AJAX variables for the frontend
        window.dvin508Courses = {
            ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('dvin508_courses_nonce'); ?>',
            moduleUrl: '<?php echo home_url('/module-1/'); ?>'
        };

        // Load course scripts but don't start automatically
        function loadCourseScripts() {
            if (!document.querySelector('script[src*="courses.js"]')) {
                const script = document.createElement('script');
                script.src = '<?php echo $plugin_url; ?>courses.js';
                script.onload = function() {
                    // Script loaded, ready for button click
                    console.log('Course scripts loaded successfully');
                };
                document.head.appendChild(script);
            }
        }

        // Start course directly without login/registration
        function scrollToCourseTop() {
            const target = document.getElementById('course-content');
            if (target) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top
                }, 400);
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        function startCourseDirectly() {
            // Show Module 1 content directly
            $('#course-content').html(`
                <div id="aa-module-view">
                    <div id="aa-module-1" class="module-content">
                        <h1>Module 1: Foundations of Web Accessibility</h1>

                        <section>
                            <h2>What is digital accessibility and why it matters</h2>
                            <p>Digital accessibility means designing and developing websites, apps, and digital content so that people with disabilities can perceive, navigate, and interact with them effectively. It's about removing barriers that prevent users from accessing information or completing tasks online.</p>
                            <h3>Why it matters:</h3>
                            <ul>
                                <li>1 in 4 adults in the U.S. has a disability</li>
                                <li>Inclusive design improves usability for everyone, yes even you! Think of closed captions in noisy spaces.</li>
                                <li>It's not just ethical – it's often a legal requirement and a brand differentiator.</li>
                            </ul>
                        </section>

                        <section>
                            <h2>Overview of disabilities and assistive technologies</h2>
                            <p>Types of Disabilities and Assistive Technologies: People experience disability across a wide spectrum. Accessible design considers permanent, temporary, and situational disabilities.</p>
                            <h3>Common disability categories:</h3>
                            <ul>
                                <li>Visual (e.g. blindness, low vision, color blindness)</li>
                                <li>Auditory (e.g. deafness, hearing loss)</li>
                                <li>Motor/Mobility (e.g. limited fine motor control, tremors)</li>
                                <li>Cognitive (e.g. dyslexia, memory impairments, ADHD)</li>
                            </ul>
                            <h3>Assistive technologies include:</h3>
                            <ul>
                                <li>Screen readers (e.g. NVDA, JAWS, VoiceOver)</li>
                                <li>Alternative input devices (e.g. switch controls, eye-tracking)</li>
                                <li>Screen magnifiers</li>
                                <li>Speech-to-text software</li>
                                <li>Captions and transcripts</li>
                            </ul>
                        </section>

                        <section>
                            <h2>Legal and business drivers (ADA, Section 508, AODA, etc.)</h2>
                            <p>Legal Drivers for Web Accessibility: These laws and regulations vary by region but share one goal: to protect the digital rights of people with disabilities.</p>
                            <h3>ADA (Americans with Disabilities Act) – U.S.</h3>
                            <p><strong>Purpose:</strong> Prohibits discrimination against individuals with disabilities in all areas of public life.</p>
                            <p><strong>Driver:</strong> Courts increasingly interpret websites as "places of public accommodation," making inaccessible digital content a legal risk.</p>
                            <p><strong>Result:</strong> Businesses, schools, and even small agencies can face lawsuits over inaccessible websites.</p>
                            <h3>Section 508 – U.S. Federal</h3>
                            <p><strong>Purpose:</strong> Requires federal agencies and contractors to ensure all digital content and tools are accessible.</p>
                            <p><strong>Driver:</strong> Mandates WCAG 2.0/2.1 compliance for websites, software, PDFs, and hardware.</p>
                            <p><strong>Result:</strong> Private companies working with government contracts must meet accessibility standards.</p>
                            <h3>Business Drivers for Accessibility</h3>
                            <p>Even when it's not mandated by law, accessibility is a competitive advantage and adds brand strength. Here's why:</p>
                            <ul>
                                <li><strong>Broader Market Reach:</strong> 1 billion+ people globally live with disabilities. Accessible websites reach more users, increase conversions, and build loyalty.</li>
                                <li><strong>SEO & Technical Benefits:</strong> Accessibility improvements (semantic HTML, alt text, keyboard nav) often boost search rankings and usability for all users.</li>
                                <li><strong>Brand Trust & Reputation:</strong> Demonstrating inclusivity through accessible design enhances public perception, especially in DEI-focused markets.</li>
                                <li><strong>Risk Mitigation:</strong> Proactive accessibility work reduces legal exposure, PR backlash, and costly retroactive fixes.</li>
                                <li><strong>Client Demand:</strong> As regulations increase, accessibility becomes a key selection factor in RFPs, contracts, and government partnerships.</li>
                            </ul>
                        </section>

                        <section>
                            <h2>Introduction to WCAG 2.2 principles (POUR)</h2>
                            <p>Principles of WCAG 2.2: POUR: The WCAG guidelines are organized around four core principles known as POUR—they're the foundation of accessible digital experiences:</p>
                            <ul>
                                <li><strong>Perceivable:</strong> Information must be presented in ways users can detect (e.g. text alternatives for images, captions for videos).</li>
                                <li><strong>Operable:</strong> Users must be able to navigate and interact with content (e.g. keyboard access, sufficient time, visible focus indicators).</li>
                                <li><strong>Understandable:</strong> The interface and content must be clear and predictable (e.g. consistent navigation, readable language, helpful error messages).</li>
                                <li><strong>Robust:</strong> Content must be compatible with a wide range of technologies, including current and future assistive tools (e.g. proper HTML semantics).</li>
                            </ul>
                        </section>

                        <div style="text-align: center; margin: 30px 0; padding: 20px;">
                            <button id="aa-next-btn" class="aa-course-btn" style="background: #22227c; color: white; border: none; padding: 15px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; display: inline-block; margin: 20px 0; box-shadow: 10px 10px 10px #d4d3d3;">Take Quiz 1</button>
                            <a href="https://508accessible.com/pricing/" target="_blank" id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 18px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3; text-decoration: none;">
                                Upgrade To Pro Version
                            </a>
                        </div>
                    </div>
                </div>
            `);
            
            scrollToCourseTop();
            
            // Add event listener for the next button
            $(document).off('click', '#aa-next-btn').on('click', '#aa-next-btn', function() {
                console.log('Next button clicked - showing Quiz 1');
                showQuiz1();
            });
            
            // Debug: Check if button exists
            setTimeout(function() {
                if ($('#aa-next-btn').length > 0) {
                    console.log('Next button found and ready');
                } else {
                    console.log('Next button not found');
                }
            }, 100);
            
            // Initialize course functionality
            if (typeof window.initializeCourse === 'function') {
                window.initializeCourse();
            }
        }
        
        // All 20 questions from courses.js
        const ALL_QUESTIONS = [
            { q: 'What is the primary goal of digital accessibility?', opts: ['To improve website speed','To enhance visual design','To ensure people with disabilities can access and interact with digital content','To reduce development costs'], a: 'C', explain: 'Accessibility is about making sure everyone, including people with disabilities, can use and benefit from digital content.' },
            { q: 'According to the CDC, how many U.S. adults have a disability?', opts: ['1 in 10','1 in 4','1 in 5','1 in 3'], a: 'B', explain: 'The CDC reports that 1 in 4 adults in the U.S. has a disability, showing how important accessibility is.' },
            { q: 'Which of the following is NOT a reason digital accessibility matters?', opts: ['It improves usability for all users','It reduces website hosting fees','It is a legal and ethical responsibility','It can increase customer satisfaction and sales'], a: 'B', explain: 'Hosting fees are unrelated to accessibility. Accessibility improves usability, meets legal standards, and boosts business outcomes.' },
            { q: 'What was the result of a retail company implementing accessible design?', opts: ['Decreased mobile traffic','12% increase in online sales','Reduced product returns','5% drop in customer satisfaction'], a: 'B', explain: 'Making a site accessible improves usability for more people, which can lead to increased sales and customer engagement.' },
            { q: 'Which of the following is a situational limitation?', opts: ['Blindness','Broken arm','Carrying groceries while using a smartphone','Dyslexia'], a: 'C', explain: 'Situational limitations are temporary or environmental challenges, like having full hands while using a device. Accessibility helps in these moments too.' },
            { q: 'Which is an example of a cognitive disability?', opts: ['Color blindness','Hearing loss','ADHD','Paralysis'], a: 'C', explain: 'ADHD affects attention and processing, making it a cognitive disability. Others listed are sensory or physical.' },
            { q: 'What assistive technology is used to read screen content aloud?', opts: ['Screen magnifier','Eye-tracking hardware','Screen reader','Visual alert'], a: 'C', explain: 'Screen readers convert text and interface elements into speech or braille, helping blind or visually impaired users navigate digital content.' },
            { q: 'Which tool helps users with hearing loss access audio content?', opts: ['Screen reader','Closed captions','Eye-tracking device','Switch system'], a: 'B', explain: 'Closed captions provide a text version of spoken content, making videos and audio accessible to users who are deaf or hard of hearing.' },
            { q: 'What does the ADA primarily address in digital accessibility?', opts: ['SEO optimization','Discrimination in physical spaces only','Discrimination in digital environments','Mobile responsiveness'], a: 'C', explain: 'The Americans with Disabilities Act (ADA) protects against discrimination in both physical and digital spaces, including websites and apps.' },
            { q: 'Section 508 applies to which group?', opts: ['Private businesses only','Educational institutions','Federal agencies and contractors','Nonprofits'], a: 'C', explain: 'Section 508 requires federal agencies and contractors to make their digital content accessible to people with disabilities.' },
            { q: 'What is the international standard for digital accessibility?', opts: ['ADA','WCAG','ARIA','HTML5'], a: 'B', explain: 'The Web Content Accessibility Guidelines (WCAG) are the globally recognized standards for making web content accessible.' },
            { q: 'What does the "P" in POUR stand for?', opts: ['Practical','Predictable','Perceivable','Portable'], a: 'C', explain: '"Perceivable" means users must be able to see, hear, or otherwise sense the content, whether through sight, sound, or assistive technology.' },
            { q: 'Which is an example of the "Operable" principle in WCAG?', opts: ['Alt text','Skip links','Plain language','Valid code'], a: 'B', explain: 'Skip links allow keyboard users to bypass repetitive navigation, making the site easier to operate without a mouse.' },
            { q: 'What does the "Understandable" principle emphasize?', opts: ['Fast loading times','Consistent navigation and clear content','Use of animations','Mobile-first design'], a: 'B', explain: 'Content should be easy to read and navigate, especially for users with cognitive disabilities. Consistency helps users predict and understand interactions.' },
            { q: 'What does "Robust" mean in the context of WCAG?', opts: ['Works only on modern browsers','Compatible with a wide range of devices and assistive technologies','Uses minimal code','Avoids all JavaScript'], a: 'B', explain: 'Robust content works across different platforms and technologies, including screen readers and other assistive tools.' },
            { q: 'Which of the following is a business benefit of accessibility?', opts: ['Higher hosting costs','Reduced SEO','Competitive advantage in procurement','Limited audience reach'], a: 'C', explain: 'Accessible products can meet government and enterprise procurement standards, giving businesses a competitive edge.' },
            { q: 'What is alt text used for?', opts: ['Styling images','Describing images for users who can\'t see them','Creating image galleries','Compressing images'], a: 'B', explain: 'Alt text provides descriptions of images for screen reader users and improves accessibility and SEO.' },
            { q: 'What does ARIA help with?', opts: ['Improving page load speed','Enhancing dynamic content accessibility','Creating responsive layouts','Reducing server load'], a: 'B', explain: 'ARIA (Accessible Rich Internet Applications) helps make interactive and dynamic content accessible to assistive technologies.' },
            { q: 'What is semantic HTML?', opts: ['HTML that uses inline styles','HTML used only for mobile devices','HTML elements used according to their intended meaning','HTML that avoids JavaScript'], a: 'C', explain: 'Semantic HTML uses tags like header, nav, and main to convey meaning, improving accessibility and SEO.' },
            { q: 'What is a keyboard trap?', opts: ['A tool for testing keyboard input','A feature that enhances keyboard navigation','A failure where users can\'t navigate out of an element using the keyboard','A shortcut for screen readers'], a: 'C', explain: 'Keyboard traps prevent users from moving focus away from an element, breaking navigation for keyboard-only users.' }
        ];

        // Function to shuffle array and pick random 10 questions
        function shuffleArray(array) {
            const shuffled = [...array];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            return shuffled;
        }

        function pickRandom10Questions() {
            return shuffleArray(ALL_QUESTIONS).slice(0, 10);
        }

        // Show Quiz 1
        function showQuiz1() {
            const selectedQuestions = pickRandom10Questions();
            
            let quizHTML = `
                <div id="aa-quiz-1" class="quiz-content">
                    <h1>Quiz: Module 1 Foundations</h1>
                    <div id="aa-quiz-questions-1">
                        <form id="aa-quiz-form-1">
            `;
            
            // Generate questions dynamically
            selectedQuestions.forEach((question, index) => {
                const questionNum = index + 1;
                quizHTML += `
                    <div class="quiz-question">
                        <h3>${questionNum}. ${question.q}</h3>
                        <div class="quiz-option">
                            <label><input type="radio" name="q${questionNum}" value="A"> A. ${question.opts[0]}</label>
                        </div>
                        <div class="quiz-option">
                            <label><input type="radio" name="q${questionNum}" value="B"> B. ${question.opts[1]}</label>
                        </div>
                        <div class="quiz-option">
                            <label><input type="radio" name="q${questionNum}" value="C"> C. ${question.opts[2]}</label>
                        </div>
                        <div class="quiz-option">
                            <label><input type="radio" name="q${questionNum}" value="D"> D. ${question.opts[3]}</label>
                        </div>
                    </div>
                `;
            });
            
            quizHTML += `
                            <div class="quiz-submit">
                                <button type="submit" class="aa-course-btn">Submit Quiz</button>
                                <a href="https://508accessible.com/pricing/" target="_blank" id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 18px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3; text-decoration: none;">
                                    Upgrade To Pro Version
                                </a>
                            </div>
                        </form>
                    </div>
                    <div id="aa-quiz-result-1" style="display: none;"></div>
                    <div class="aa-quiz-next-actions" style="display: none;">
                        <button id="aa-quiz-retake-1" class="aa-course-btn aa-course-btn-secondary" style="display: none;">Retake Quiz</button>
                        <button id="aa-quiz-back-module-1" class="aa-course-btn aa-course-btn-secondary" style="display: none;">Back to Module 1</button>
                        <button id="aa-quiz-continue-1" class="aa-course-btn" disabled>Continue</button>
                    </div>
                </div>
            `;
            
            $('#course-content').html(quizHTML);
            scrollToCourseTop();
            
            // Store selected questions for scoring
            window.selectedQuestions = selectedQuestions;
            
            // Add quiz form submission handler
            $(document).off('submit', '#aa-quiz-form-1').on('submit', '#aa-quiz-form-1', function(e) {
                e.preventDefault();
                
                let correct = 0;
                let total = window.selectedQuestions.length;
                
                // Reset previous feedback
                $('.quiz-question').removeClass('quiz-correct quiz-incorrect');
                $('.quiz-feedback').remove();
                
                // Check answers against selected questions and build per-question feedback
                window.selectedQuestions.forEach((question, index) => {
                    const questionNum = index + 1;
                    const selected = $(`input[name="q${questionNum}"]:checked`).val();
                    const $questionBlock = $('.quiz-question').eq(index);
                    
                    // Map answer letter to index to find text
                    const answerIndexMap = { A: 0, B: 1, C: 2, D: 3 };
                    const correctLetter = question.a;
                    const correctText = question.opts[answerIndexMap[correctLetter]];
                    
                    let selectedText = '';
                    if (selected) {
                        const selectedIndex = answerIndexMap[selected];
                        selectedText = typeof selectedIndex !== 'undefined' ? question.opts[selectedIndex] : '';
                    }
                    
                    const isCorrect = selected === correctLetter;
                    if (isCorrect) {
                        correct++;
                        $questionBlock.addClass('quiz-correct');
                    } else {
                        $questionBlock.addClass('quiz-incorrect');
                    }
                    
                    // Build feedback HTML similar to Pro version behavior
                    const feedbackHtml = `
                        <div class="quiz-feedback">
                            <div class="${isCorrect ? 'quiz-feedback-correct' : 'quiz-feedback-incorrect'}">
                                ${isCorrect ? 'Correct answer selected.' : 'Incorrect answer.'}
                            </div>
                            ${selected ? `<div><strong>Your answer:</strong> ${selected}. ${selectedText}</div>` : '<div><strong>Your answer:</strong> Not answered</div>'}
                            <div><strong>Correct answer:</strong> ${correctLetter}. ${correctText}</div>
                        </div>
                    `;
                    
                    $questionBlock.append(feedbackHtml);
                });
                
                const percentage = Math.round((correct / total) * 100);
                const passed = percentage >= 70;
                
                // Show results
                $('#aa-quiz-result-1').html(`
                    <div class="quiz-results">
                        <h3>Quiz Results</h3>
                        <div class="quiz-score">
                            <strong>Score:</strong> ${correct}/${total} (${percentage}%)
                        </div>
                        <p class="${passed ? 'aa-quiz-pass' : 'aa-quiz-fail'}">
                            ${passed ? 'Congratulations! You passed the quiz!' : 'You need to score at least 70% to pass. Please review the material and try again.'}
                        </p>
                    </div>
                `).show();
                
                $('.aa-quiz-next-actions').show();
                $('#aa-quiz-continue-1').prop('disabled', !passed);
                $('#aa-quiz-retake-1, #aa-quiz-back-module-1').toggle(!passed);
                
                if (passed) {
                    // After passing, show upgrade section
                    $(document).off('click', '#aa-quiz-continue-1').on('click', '#aa-quiz-continue-1', function() {
                        showUpgradeSection();
                    });
                } else {
                    $(document).off('click', '#aa-quiz-retake-1').on('click', '#aa-quiz-retake-1', function() {
                        showQuiz1();
                    });
                    $(document).off('click', '#aa-quiz-back-module-1').on('click', '#aa-quiz-back-module-1', function() {
                        startCourseDirectly();
                    });
                }
            });
        }
        
        // Show upgrade section after quiz completion
        function showUpgradeSection() {
            $('#course-content').html(`
                <div id="aa-upgrade-section" class="congratulations-content">
                    <div class="congratulations-header">
                        <h1>Congratulations!</h1>
                        <h2>You've completed Module 1: Foundations of Web Accessibility</h2>
                        <p class="congratulations-subtitle">You now understand the fundamentals of digital accessibility and its importance.</p>
                    </div>
                    
                    <div class="upgrade-section">
                        <h3>Ready to become an accessibility expert?</h3>
                        <p>Continue your learning journey with our comprehensive Pro course covering all aspects of web accessibility.</p>
                        
                        <div class="pro-benefits">
                            <h4>What you'll get with Pro:</h4>
                            <div class="benefits-grid">
                                <div class="benefit-item">
                                    <div class="benefit-text">
                                        <strong>5 Additional Modules</strong>
                                        <span>WCAG 2.2, Design, Development, Testing & Implementation</span>
                                    </div>
                                </div>
                                <div class="benefit-item">
                                    <div class="benefit-text">
                                        <strong>Advanced Tools</strong>
                                        <span>Testing techniques and remediation strategies</span>
                                    </div>
                                </div>
                                <div class="benefit-item">
                                    <div class="benefit-text">
                                        <strong>User Management</strong>
                                        <span>Track progress and manage team learning</span>
                                    </div>
                                </div>
                                <div class="benefit-item">
                                    <div class="benefit-text">
                                        <strong>Certificate</strong>
                                        <span>Professional completion certificate</span>
                                    </div>
                                </div>
                                <div class="benefit-item">
                                    <div class="benefit-text">
                                        <strong>Priority Support</strong>
                                        <span>Expert guidance and updates</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="upgrade-cta">
                            <a href="https://508accessible.com/pricing/" target="_blank" id="start-course-btn" class="button button-primary" style="background: #22227c; color: white; border: none; padding: 18px 30px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; box-shadow: 10px 10px 10px #d4d3d3; text-decoration: none;">
                                Upgrade To Pro Version
                            </a>
                            <p class="upgrade-note">Join thousands of professionals learning accessibility best practices</p>
                        </div>
                    </div>
                </div>
            `);
            scrollToCourseTop();
        }

        // Handle start course button click
        function handleStartCourse() {
            $('#course-outline').hide();
            startCourseDirectly();
        }

        // Start loading when page is ready
        $(document).ready(function() {
            // Initialize course when the "Start Course" button is clicked
            $('#start-course-btn').on('click', function() {
                handleStartCourse();
            });

            // Load course scripts but don't start automatically
            loadCourseScripts();
        });
    </script>
</body>
</html>