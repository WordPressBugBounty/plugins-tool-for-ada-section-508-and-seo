<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style type="text/css">
/* Minimal styling for the tab and content */
#accessibility-courses { padding: 12px 12px 30px; }
#accessibility-courses h1 { font-size:20px; margin:0 0 12px 0; }
#accessibility-courses h2 { font-size:16px; margin:12px 0 8px; }
#accessibility-courses ul { margin: 8px 0 16px 10px; list-style: inside; }
.aa-course-btn {
    text-decoration: none;
    font-size: 18px;
    background: #0048ff;
    border-radius: 10px;
    padding: 8px 16px;
    color: white;
    margin-top: 50px;
    border: none;
    cursor: pointer;
}
.aa-course-btn:hover {
    color: #fff;
    background: #0036cc;
}
.upgrade-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 20px;
    text-align: center;
    border-radius: 10px;
    margin: 30px 0;
}
.upgrade-section h2 {
    color: white;
    margin-bottom: 15px;
}
.upgrade-btn {
    background: #ff6b35;
    color: white;
    padding: 15px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    display: inline-block;
    margin-top: 20px;
    transition: background 0.3s ease;
}
.upgrade-btn:hover {
    background: #e55a2b;
    color: white;
    text-decoration: none;
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
</style>
<div id="accessibility-courses" class="content" role="tabpanel" aria-labelledby="tab-accessibility">
  <h1>Web Accessibility &amp; WCAG 2.2 Compliance for Agencies</h1>

  <p><strong>Course Objective:</strong> Equip agency teams with the knowledge and tools to integrate accessibility best practices into their workflows, ensuring WCAG 2.2 Level A and AA compliance across client projects.</p>

  <h2>Module 1: Foundations of Web Accessibility</h2>
  <ul>
    <li>What is digital accessibility and why it matters</li>
    <li>Overview of disabilities and assistive technologies</li>
    <li>Legal and business drivers (ADA, Section 508, AODA, etc.)</li>
    <li>Introduction to WCAG 2.2 principles (POUR)</li>
  </ul>

  <a href="#" id="aa-start-course" class="aa-course-btn">Start Course</a>
</div>

<!-- Module 1 Content -->
<div id="aa-module-view" style="display: none;">
  <div id="aa-module-1">
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

    <div style="text-align: center; margin: 30px 0;">
      <button id="aa-next-btn" class="aa-course-btn">Next: Take Quiz 1</button>
    </div>
  </div>
</div>

<!-- Quiz 1 -->
<div id="aa-quiz-1" style="display: none;">
  <h1>Quiz: Module 1 Foundations</h1>
  <div id="aa-quiz-questions-1"></div>
  <div id="aa-quiz-result-1"></div>
  <div class="aa-quiz-next-actions" style="display: none;">
    <button id="aa-quiz-continue-1" class="aa-course-btn" disabled>Continue</button>
  </div>
</div>

<!-- Upgrade Section (shown after Quiz 1 completion) -->
<div id="aa-upgrade-section" style="display: none;">
  <div class="upgrade-section">
    <h2>🎉 Congratulations! You've completed Module 1</h2>
    <p>You've learned the foundations of web accessibility. To continue with the full course including Modules 2-6 and advanced topics, upgrade to our Pro version.</p>
    <h3>What you'll get with Pro:</h3>
    <ul style="text-align: left; max-width: 600px; margin: 20px auto;">
      <li>5 additional modules covering WCAG 2.2, design, development, and testing</li>
      <li>Advanced accessibility testing tools and techniques</li>
      <li>User management and progress tracking</li>
      <li>Certificate of completion</li>
      <li>Priority support and updates</li>
    </ul>
    <a href="https://508accessible.com/web-accessibility-pro-plugin/" target="_blank" class="upgrade-btn">Upgrade to Pro for Full Access</a>
  </div>
</div>
