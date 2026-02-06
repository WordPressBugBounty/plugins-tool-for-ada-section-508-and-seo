(function($){
	'use strict';
	
	var TWELVE_HOURS_MS = 12 * 60 * 60 * 1000;
	var EMBEDDED = typeof window.location !== 'undefined' && window.location.pathname.includes('wp-admin') ? 'admin' : 'frontend';
	var PASS_THRESHOLD = 6;
	var QUIZ_LENGTH = 10;
	
	// Get AJAX variables from WordPress
	var ajaxUrl = (window.dvin508Courses && window.dvin508Courses.ajaxUrl) || '/wp-admin/admin-ajax.php';
	var nonce = (window.dvin508Courses && window.dvin508Courses.nonce) || '';
	
	// If nonce is still empty, try to get it from a meta tag or generate a fallback
	if (!nonce) {
		// Try to get nonce from meta tag (common WordPress pattern)
		var metaNonce = document.querySelector('meta[name="wp-nonce"]');
		if (metaNonce) {
			nonce = metaNonce.getAttribute('content');
		} else {
			// Generate a simple fallback (this won't work for security but helps debug)
			nonce = 'fallback-nonce-' + Date.now();
		}
	}

	var ALL_QUESTIONS = [
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
		{ q: 'What is alt text used for?', opts: ['Styling images','Describing images for users who can’t see them','Creating image galleries','Compressing images'], a: 'B', explain: 'Alt text provides descriptions of images for screen reader users and improves accessibility and SEO.' },
		{ q: 'What does ARIA help with?', opts: ['Improving page load speed','Enhancing dynamic content accessibility','Creating responsive layouts','Reducing server load'], a: 'B', explain: 'ARIA (Accessible Rich Internet Applications) helps make interactive and dynamic content accessible to assistive technologies.' },
		{ q: 'What is semantic HTML?', opts: ['HTML that uses inline styles','HTML used only for mobile devices','HTML elements used according to their intended meaning','HTML that avoids JavaScript'], a: 'C', explain: 'Semantic HTML uses tags like header, nav, and main to convey meaning, improving accessibility and SEO.' },
		{ q: 'What is a keyboard trap?', opts: ['A tool for testing keyboard input','A feature that enhances keyboard navigation','A failure where users can’t navigate out of an element using the keyboard','A shortcut for screen readers'], a: 'C', explain: 'Keyboard traps prevent users from moving focus away from an element, breaking navigation for keyboard-only users.' }
	];

	function shuffle(array){
		for (var i = array.length - 1; i > 0; i--) {
			var j = Math.floor(Math.random() * (i + 1));
			var t = array[i]; array[i] = array[j]; array[j] = t;
		}
		return array;
	}

	function pickRandom10(){
		var copy = ALL_QUESTIONS.slice(0);
		shuffle(copy);
		return copy.slice(0, QUIZ_LENGTH);
	}
	// Module 2-6 question functions removed - free plugin only uses Module 1

	function getActiveUserId(){
		// No authentication needed - return null
		return null;
	}
	function progressKey(){
		// Use a simple key for anonymous users
		return 'dvin508CourseProgress:anonymous';
	}
	function setProgressStage(stage){
		try {
			var key = progressKey();
			localStorage.setItem(key, JSON.stringify({ stage: stage, ts: Date.now() }));
		} catch(e){
			// Silent error handling
		}
	}
	function getProgressStage(){
		try {
			var key = progressKey();
			if (!key){ return null; }
			var raw = localStorage.getItem(key);
			if (!raw){ return null; }
			var obj = JSON.parse(raw);
			return obj && obj.stage || null;
		} catch(e){ return null; }
	}
	
	// Database functions removed - no authentication needed

	var currentQuiz = null;
	var currentQuizM2 = null;
	var currentQuizM3 = null;
	var currentQuizM4 = null;
	var currentQuizM5 = null;
	var currentQuizM6 = null;

	function renderQuiz(container, questions){
		var html = '';
		questions.forEach(function(item, idx){
			var qId = 'q' + (idx+1);
			html += '<fieldset style="margin-bottom:12px;">';
			html += '<legend>' + (idx+1) + '. ' + item.q + '</legend>';
			['A','B','C','D'].forEach(function(letter, optIdx){
				var opt = item.opts[optIdx];
				html += '<div>' +
					'<label>' +
						'<input type="radio" name="' + qId + '" value="' + letter + '"> ' + letter + '. ' + opt +
					'</label>' +
				'</div>';
			});
			html += '</fieldset>';
		});
		$(container).html(html);
	}

	function scoreQuiz(questions, rootSelector){
		var $root = rootSelector ? $(rootSelector) : $(document);
		var correct = 0;
		var details = [];
		questions.forEach(function(item, idx){
			var qId = 'q' + (idx+1);
			var given = ($root.find('input[name="' + qId + '"]:checked').val() || '').toUpperCase();
			var isRight = (given === item.a);
			if (isRight) { correct++; }
			details.push({ idx: idx+1, correct: isRight, expected: item.a, given: given, explain: item.explain });
		});
		return { correct: correct, wrong: questions.length - correct, details: details };
	}

	function showResults(container, result){
		var out = 'Correct: ' + result.correct + ' / ' + QUIZ_LENGTH + ' | Wrong: ' + result.wrong;
		out += '<div style="margin-top:10px;">';
		result.details.forEach(function(d){
			out += '<div style="margin-bottom:6px;">' +
				'#' + d.idx + ': ' + (d.correct ? '<strong style="color:green;">Correct</strong>' : '<strong style="color:#b60000;">Wrong</strong>') +
				' (Your answer: ' + (d.given || '-') + ', Correct: ' + d.expected + ')' +
				'<div>' + d.explain + '</div>' +
			'</div>';
		});
		out += '</div>';
		$(container).html(out);
	}

	function hideOutline(){
		$('#accessibility-courses').hide();
		$('.aa-course-btn').hide();
	}

	function goToQuiz(){
		hideOutline();
		$('#aa-module-view').hide();
		$('#aa-quiz-1').show();
		currentQuiz = pickRandom10();
		renderQuiz('#aa-quiz-questions-1', currentQuiz);
		$('#aa-quiz-result-1').empty();
		$('.aa-quiz-next-actions').hide();
		setProgressStage('quiz1');
	}
	// Module 2-6 quiz functions removed - free plugin only has Quiz 1

	function showModule(index){
		if (!EMBEDDED){ window.location.href = getModuleUrl(); return; }
		$('#accessibility-courses').hide();
		$('.aa-course-btn').hide();
		$('#aa-auth-modal').hide();
		$('#aa-module-view').show();
		$('#aa-module-1').toggle(index === 1);
		$('#aa-prev-btn').hide(); // No previous button for Module 1
		setProgressStage('module1');
	}
	// expose for outside callers if any inline handlers reference it
	window.showModule = showModule;

	function getModuleUrl(){
		return (window.dvin508Courses && window.dvin508Courses.moduleUrl) || '/';
	}

	function nowMs(){ return Date.now(); }

	function isClientLoggedIn(){
		// No authentication needed - always return true
		return true;
	}
	// Login functions removed - no authentication needed

	function isLoggedIn(){
		return isClientLoggedIn();
	}

	function proceedToCourse(){
		// Start directly with Module 1
		hideOutline();
		showModule(1);
	}
	// expose for outside callers if any inline handlers reference it
	window.proceedToCourse = proceedToCourse;

	var lastFocused = null;
	function openModal(){
		$('#aa-auth-modal').show().attr('aria-hidden', 'false');
		$('#aa-login-username').trigger('focus');
	}
	function closeModal(){
		$('#aa-auth-modal').hide().attr('aria-hidden', 'true');
		if (lastFocused) { try { lastFocused.focus(); } catch(e){} }
	}

	function switchTab(target){
		if (target === 'login'){
			$('#aa-login-tab').addClass('aa-tab--active').attr('aria-selected','true');
			$('#aa-register-tab').removeClass('aa-tab--active').attr('aria-selected','false');
			$('#aa-login-panel').removeClass('aa-panel--hidden');
			$('#aa-register-panel').addClass('aa-panel--hidden');
			$('#aa-login-username').trigger('focus');
		} else {
			$('#aa-register-tab').addClass('aa-tab--active').attr('aria-selected','true');
			$('#aa-login-tab').removeClass('aa-tab--active').attr('aria-selected','false');
			$('#aa-register-panel').removeClass('aa-panel--hidden');
			$('#aa-login-panel').addClass('aa-panel--hidden');
			$('#aa-reg-username').trigger('focus');
		}
	}

	$(document).on('click', '#aa-start-course', function(e){
		if (e && typeof e.preventDefault === 'function') { e.preventDefault(); }
		try { window.getSelection && window.getSelection().removeAllRanges && window.getSelection().removeAllRanges(); } catch(err){}
		if (isLoggedIn()){
			proceedToCourse();
			return false;
		}
		$('#aa-auth-modal').show().attr('aria-hidden', 'false');
		$('#aa-login-username').trigger('focus');
		return false;
	});

	$(document).on('click', '#aa-auth-close', function(){
		closeModal();
	});

	$(document).on('click', '#aa-register-tab', function(){ 
		$('#aa-login-tab').removeClass('aa-tab--active').attr('aria-selected','false');
		$('#aa-register-tab').addClass('aa-tab--active').attr('aria-selected','true');
		$('#aa-register-panel').removeClass('aa-panel--hidden');
		$('#aa-login-panel').addClass('aa-panel--hidden');
		$('#aa-reg-username').trigger('focus');
	});
	$(document).on('click', '#aa-login-tab', function(){ 
		$('#aa-register-tab').removeClass('aa-tab--active').attr('aria-selected','false');
		$('#aa-login-tab').addClass('aa-tab--active').attr('aria-selected','true');
		$('#aa-login-panel').removeClass('aa-panel--hidden');
		$('#aa-register-panel').addClass('aa-panel--hidden');
		$('#aa-login-username').trigger('focus');
	});
	$(document).on('click', '#aa-switch-to-register', function(e){ e.preventDefault(); $('#aa-register-tab').trigger('click'); });
	$(document).on('click', '#aa-switch-to-login', function(e){ e.preventDefault(); $('#aa-login-tab').trigger('click'); });

	$('#aa-login-form').on('submit', function(e){
		e.preventDefault();
		var $fb = $('#aa-login-feedback').text('');
		var data = {
			action: 'dvin508_courses_login',
			nonce: (window.dvin508Courses && window.dvin508Courses.nonce) || '',
			username: $('#aa-login-username').val(),
			password: $('#aa-login-password').val()
		};
		$.post((window.dvin508Courses && window.dvin508Courses.ajaxUrl) || '', data)
			.done(function(resp){
				if (resp && resp.success){
					setClientLoggedIn(resp.data && resp.data.user);
					$fb.text(resp.data && resp.data.message ? resp.data.message : 'Login successful.');
					$('#aa-auth-modal').hide().attr('aria-hidden', 'true');
					proceedToCourse();
				} else {
					$fb.text(resp && resp.data && resp.data.message ? resp.data.message : 'Login failed.');
					clearClientLogin();
				}
			})
			.fail(function(){ $fb.text('Network error. Please try again.'); });
	});

	$('#aa-register-form').on('submit', function(e){
		e.preventDefault();
		var $fb = $('#aa-register-feedback').text('');
		var data = {
			action: 'dvin508_courses_register',
			nonce: (window.dvin508Courses && window.dvin508Courses.nonce) || '',
			username: $('#aa-reg-username').val(),
			email: $('#aa-reg-email').val(),
			password: $('#aa-reg-password').val(),
			confirm_password: $('#aa-reg-confirm').val()
		};
		$.post((window.dvin508Courses && window.dvin508Courses.ajaxUrl) || '', data)
			.done(function(resp){
				if (resp && resp.success){
					$fb.text(resp.data && resp.data.message ? resp.data.message : 'Registration successful.');
					$('#aa-login-tab').trigger('click');
				} else {
					$fb.text(resp && resp.data && resp.data.message ? resp.data.message : 'Registration failed.');
				}
			})
			.fail(function(){ $fb.text('Network error. Please try again.'); });
	});

	// Embedded Next/Previous
	$(document).on('click', '#aa-next-btn', function(){
		if ($('#aa-module-1').is(':visible')){
			goToQuiz();
			return false;
		}
		if ($('#aa-module-2').is(':visible')){
			goToQuiz2();
			return false;
		}
		if ($('#aa-module-3').is(':visible')){
			$('#aa-module-view').hide();
			$('#aa-quiz-3').show();
			currentQuizM3 = pickRandom10M3();
			renderQuiz('#aa-quiz-questions-3', currentQuizM3);
			$('#aa-quiz-result-3').empty();
			$('.aa-quiz-next-actions-3').hide();
			setProgressStage('quiz3');
			return false;
		}
		if ($('#aa-module-4').is(':visible')){
			$('#aa-module-view').hide();
			$('#aa-quiz-4').show();
			currentQuizM4 = pickRandom10M4();
			renderQuiz('#aa-quiz-questions-4', currentQuizM4);
			$('#aa-quiz-result-4').empty();
			$('.aa-quiz-next-actions-4').hide();
			setProgressStage('quiz4');
			return false;
		}
		if ($('#aa-module-5').is(':visible')){
			$('#aa-module-view').hide();
			$('#aa-quiz-5').show();
			currentQuizM5 = pickRandom10M5();
			renderQuiz('#aa-quiz-questions-5', currentQuizM5);
			$('#aa-quiz-result-5').empty();
			$('.aa-quiz-next-actions-5').hide();
			setProgressStage('quiz5');
			return false;
		}
		if ($('#aa-module-6').is(':visible')){
			$('#aa-module-view').hide();
			$('#aa-quiz-6').show();
			currentQuizM6 = pickRandom10M6();
			renderQuiz('#aa-quiz-questions-6', currentQuizM6);
			$('#aa-quiz-result-6').empty();
			$('.aa-quiz-next-actions-6').hide();
			setProgressStage('quiz6');
			return false;
		}
	});
	$(document).on('click', '#aa-prev-btn', function(){
		// Only Module 1 available in free plugin
	});

	$('#aa-quiz-form-1').on('submit', function(e){
		e.preventDefault();
		if (!currentQuiz){ return; }
		var result = scoreQuiz(currentQuiz, '#aa-quiz-1');
		showResults('#aa-quiz-result-1', result);
		var passed = (result.correct >= PASS_THRESHOLD);
		$('.aa-quiz-next-actions').show();
		$('#aa-quiz-continue-1').prop('disabled', !passed);
		if (!passed){ $('#aa-quiz-continue-1').attr('aria-disabled','true'); } else { $('#aa-quiz-continue-1').removeAttr('aria-disabled'); }
	});

	$('#aa-quiz-retake-1').on('click', function(){
		$('#aa-quiz-1').hide();
		showModule(1);
	});

	$('#aa-quiz-continue-1').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-1').hide();
		$('#aa-upgrade-section').show();
	});

	// Quiz 2-6 handlers removed - free version only has Quiz 1

	$('#aa-quiz-retake-2').on('click', function(){
		$('#aa-quiz-2').hide();
		showModule(2);
	});

	$('#aa-quiz-continue-2').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-2').hide();
		$('#aa-module-view').show();
		$('#aa-module-1, #aa-module-2').hide();
		$('#aa-module-3').show();
		$('#aa-prev-btn').show();
		setProgressStage('module3');
	});

	$(document).on('click', '#aa-complete-next', function(){
		hideOutline();
		$('#aa-course-complete').hide();
		$('#aa-module-view').show();
		$('#aa-module-1, #aa-module-2').hide();
		$('#aa-module-3').show();
		$('#aa-prev-btn').show();
		setProgressStage('module3');
	});

	// Quiz 3 handler removed - free version only has Quiz 1

	$('#aa-quiz-retake-3').on('click', function(){
		$('#aa-quiz-3').hide();
		showModule(3);
	});

	$('#aa-quiz-continue-3').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-3').hide();
		hideOutline();
		$('#aa-module-view').show();
		$('#aa-module-1, #aa-module-2, #aa-module-3').hide();
		$('#aa-module-4').show();
		$('#aa-prev-btn').show();
		setProgressStage('module4');
	});

	// Quiz 4 handler removed - free version only has Quiz 1

	$('#aa-quiz-retake-4').on('click', function(){
		$('#aa-quiz-4').hide();
		showModule(4);
	});

	$('#aa-quiz-continue-4').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-4').hide();
		hideOutline();
		$('#aa-module-view').show();
		$('#aa-module-1, #aa-module-2, #aa-module-3, #aa-module-4').hide();
		$('#aa-module-5').show();
		$('#aa-prev-btn').show();
		setProgressStage('module5');
	});

	// Quiz 5 handler removed - free version only has Quiz 1

	$('#aa-quiz-retake-5').on('click', function(){
		$('#aa-quiz-5').hide();
		showModule(5);
	});

	$('#aa-quiz-continue-5').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-5').hide();
		hideOutline();
		$('#aa-module-view').show();
		$('#aa-module-1, #aa-module-2, #aa-module-3, #aa-module-4, #aa-module-5').hide();
		$('#aa-module-6').show();
		$('#aa-prev-btn').show();
		setProgressStage('module6');
	});

	// Quiz 6 handler removed - free version only has Quiz 1

	$('#aa-quiz-retake-6').on('click', function(){
		$('#aa-quiz-6').hide();
		showModule(6);
	});

	$('#aa-quiz-continue-6').on('click', function(){
		if ($(this).is(':disabled')){ return; }
		$('#aa-quiz-6').hide();
		$('#aa-course-complete').show();
		setProgressStage('complete');
	});

	// Resume last page automatically if already logged in on load
	$(function(){
		// Do not auto-open or auto-resume; let Start Course control the flow
		// Users can click Start Course to resume or login as needed
	});

	// Close on Escape
	$(document).on('keydown', function(e){
		if (e.key === 'Escape') { closeModal(); }
	});

	// Expose functions to global scope for external access
	window.showModule = showModule;
	window.proceedToCourse = proceedToCourse;
	window.initializeCourse = initializeCourse;
	
	// Initialize course functionality
	function initializeCourse() {
		// Start course directly without authentication
		proceedToCourse();
	}

})(jQuery);
