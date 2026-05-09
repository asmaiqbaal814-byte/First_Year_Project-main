<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedMeal</title>
    <link rel="icon" href="images\titleLogo.png" type="image/png" sizes="19x19">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Noto+Sans+Sinhala&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
        /* Loading animation */
        .loading-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            padding: 40px 20px;
        }
        .loading-steps {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 340px;
        }
        .loading-step {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #4a6358;
            opacity: 0;
            transform: translateX(-12px);
            animation: stepIn 0.4s ease forwards;
        }
        .loading-step:nth-child(1) { animation-delay: 0.1s; }
        .loading-step:nth-child(2) { animation-delay: 0.5s; }
        .loading-step:nth-child(3) { animation-delay: 0.9s; }
        .loading-step:nth-child(4) { animation-delay: 1.3s; }
        @keyframes stepIn {
            to { opacity: 1; transform: translateX(0); }
        }
        .step-dot {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #019C78;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
            animation: dotPulse 1s ease infinite;
        }
        .loading-step:nth-child(1) .step-dot { animation-delay: 0.1s; }
        .loading-step:nth-child(2) .step-dot { animation-delay: 0.7s; }
        .loading-step:nth-child(3) .step-dot { animation-delay: 1.3s; }
        .loading-step:nth-child(4) .step-dot { animation-delay: 1.9s; }
        @keyframes dotPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(1,156,120,0.4); }
            50%      { box-shadow: 0 0 0 6px rgba(1,156,120,0); }
        }
        .loading-wrap p {
            color: #019C78;
            font-weight: 800;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        /* Result divider — sits outside form-group */
        .results-section {
            flex:1;
            padding: 10px;

        }
        .result-divider {
            display: none;
            align-items: center;
            gap: 12px;
            margin: 24px 0 16px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #019C78;
        }
        .result-divider::before,
        .result-divider::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #a8e8cf, transparent);
        }

        /* Results wrapper — inside form-group, inherits its width */
        #results {
            animation: fadeUp 0.4s ease;
        }
        #results {
            max-width: 1200px;
            margin: 0 auto;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Profile chips */
        .profile-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        .profile-chip {
            background: #e8fdf5;
            border: 1.5px solid #a8e8cf;
            color: #1a6644;
            border-radius: 20px;
            padding: 4px 13px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .profile-chip b { color: #019C78; }

        /* Section heading */
        .res-heading {
            font-size: 0.95rem;
            font-weight: 800;
            color: #019C78;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .res-heading::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, #a8e8cf, transparent);
            border-radius: 2px;
        }

        /* Meal timeline */
        .meal-timeline {
            position: relative;
            display: flex;
            flex-direction: column;
            margin-bottom: 28px;
        }
        .meal-timeline::before {
            content: '';
            position: absolute;
            left: 38px; top: 28px; bottom: 28px;
            width: 2px;
            background: linear-gradient(180deg, #a8e8cf, #019C78 50%, #a8e8cf);
            border-radius: 2px;
            z-index: 0;
        }
        .meal-row {
            display: flex;
            align-items: flex-start;
            position: relative; z-index: 1;
            padding: 8px 0;
            animation: slideIn 0.45s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .meal-row:nth-child(1){animation-delay:0.05s}
        .meal-row:nth-child(2){animation-delay:0.12s}
        .meal-row:nth-child(3){animation-delay:0.19s}
        .meal-row:nth-child(4){animation-delay:0.26s}
        .meal-row:nth-child(5){animation-delay:0.33s}
        @keyframes slideIn {
            from { opacity:0; transform:translateX(-14px); }
            to   { opacity:1; transform:translateX(0); }
        }
        .meal-node {
            width: 54px; height: 54px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
            border: 3px solid #fff;
            box-shadow: 0 4px 14px rgba(1,156,120,0.15);
            position: relative; z-index: 2;
            margin-left: 12px;
        }
        .meal-bubble {
            flex: 1;
            background: #fff;
            border-radius: 16px;
            padding: 12px 18px;
            margin-left: 14px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.07);
            border-left: 4px solid #019C78;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .meal-bubble:hover {
            transform: translateX(4px);
            box-shadow: 0 6px 20px rgba(1,156,120,0.13);
        }
        .meal-time-label {
            font-size: 0.65rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.12em;
            color: #019C78; margin-bottom: 3px;
        }
        .meal-name-text {
            font-size: 0.88rem; font-weight: 700;
            color: #1a2e25; line-height: 1.4;
        }
        @media (max-width: 480px) {
            .meal-timeline::before { left: 20px; }
            .meal-node { width:40px; height:40px; font-size:1.1rem; margin-left:0; }
            .meal-bubble { margin-left:10px; padding:10px 13px; }
            .meal-name-text { font-size:0.82rem; }
        }

        /* Food guide */
        .food-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) { .food-cols { grid-template-columns: 1fr; } }

        .food-card {
            border-radius: 18px; padding: 18px 16px;
            border: 1.5px solid transparent;
            position: relative; overflow: hidden;
            animation: fadeUp 0.4s ease both;
        }
        .food-card::after {
            content: attr(data-mark);
            position: absolute; bottom:-12px; right:4px;
            font-size:5rem; line-height:1;
            opacity:0.055; pointer-events:none; user-select:none;
        }
        .food-card.fc-green {
            background: linear-gradient(145deg, #f0fdf8, #fff 55%);
            border-color: #b8e8d0;
        }
        .food-card.fc-red {
            background: linear-gradient(145deg, #fff5f5, #fff 55%);
            border-color: #f5c0c0;
        }
        .food-card-hdr {
            display: flex; align-items: center; gap: 9px;
            margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 1.5px dashed #e8e8e8;
        }
        .food-card-ico {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .fc-green .food-card-ico { background: #d4f5e8; }
        .fc-red   .food-card-ico { background: #fde0e0; }
        .food-card-ttl {
            font-size: 0.75rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
        }
        .fc-green .food-card-ttl { color: #0a7a56; }
        .fc-red   .food-card-ttl { color: #c03030; }
        .tag-wrap { display: flex; flex-wrap: wrap; gap: 7px; }
        .food-tag {
            display: inline-flex; align-items: flex-start; gap: 5px;
            padding: 5px 11px; border-radius: 999px;
            font-size: 0.78rem; font-weight: 700; line-height: 1.35;
            animation: chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .fc-green .food-tag { background:#e6faf2; color:#0a6644; border:1px solid #a8e8c8; }
        .fc-red   .food-tag { background:#fff0f0; color:#9a2020; border:1px solid #f5b8b8; }
        .tag-dot { margin-top:4px; width:5px; height:5px; border-radius:50%; flex-shrink:0; }
        .fc-green .tag-dot { background:#019C78; }
        .fc-red   .tag-dot { background:#e05252; }
        .food-tag:nth-child(1){animation-delay:0.04s}.food-tag:nth-child(2){animation-delay:0.09s}
        .food-tag:nth-child(3){animation-delay:0.14s}.food-tag:nth-child(4){animation-delay:0.19s}
        .food-tag:nth-child(5){animation-delay:0.24s}.food-tag:nth-child(6){animation-delay:0.29s}
        .food-tag:nth-child(7){animation-delay:0.34s}
        @keyframes chipPop {
            from { transform:scale(0.7); opacity:0; }
            to   { transform:scale(1);   opacity:1; }
        }

        /* Nutrition */
        .nutr-box {
            background: #fff; border-radius: 18px;
            padding: 20px 18px; margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(74,126,232,0.09);
            border-top: 4px solid #4a7ee8;
            animation: fadeUp 0.4s ease 0.15s both;
        }
        .nutr-dis-label {
            font-size: 0.68rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.12em;
            color: #4a7ee8; margin: 0 0 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .nutr-dis-label::before {
            content:''; width:7px; height:7px;
            border-radius:50%; background:#4a7ee8; flex-shrink:0;
        }
        .nutr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px,1fr));
            gap: 8px; margin-bottom: 14px;
        }
        .nutr-grid:last-child { margin-bottom: 0; }
        .nutr-pill {
            background: #eef4ff; border: 1.5px solid #c5d8f8;
            border-radius: 12px; padding: 10px 12px;
            display: flex; flex-direction: column; gap: 2px;
            animation: chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .nutr-pill:nth-child(1){animation-delay:0.04s}.nutr-pill:nth-child(2){animation-delay:0.09s}
        .nutr-pill:nth-child(3){animation-delay:0.14s}.nutr-pill:nth-child(4){animation-delay:0.19s}
        .nutr-pill:nth-child(5){animation-delay:0.24s}.nutr-pill:nth-child(6){animation-delay:0.29s}
        .nutr-lbl { font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#4a7ee8; }
        .nutr-val { font-size:0.84rem; font-weight:700; color:#1a2e60; line-height:1.3; }
        @media(max-width:480px){ .nutr-grid { grid-template-columns:1fr 1fr; } }

        /* Tip banner */
        .tip-banner {
            background: linear-gradient(135deg, #019C78, #0dbf94);
            border-radius: 16px; padding: 18px 22px;
            color: #fff; font-size: 0.9rem; font-weight: 600;
            line-height: 1.65; margin-bottom: 20px;
            display: flex; gap: 12px; align-items: flex-start;
            animation: fadeUp 0.4s ease 0.2s both;
        }
        .tip-icon { font-size: 1.8rem; flex-shrink: 0; }

        /* Error box */
        .err-box {
            background: #fff0f0; border: 2px solid #e05c5c;
            border-radius: 16px; padding: 24px 20px;
            text-align: center; color: #b02020;
            font-weight: 700; font-size: 0.95rem;
        }
        .err-box .err-icon { font-size:2rem; margin-bottom:8px; display:block; }
    </style>

    <script>
        function navigateToSignIn() {
            window.location.href = 'signin.html';
        }
        function setActiveSearchButton(buttonId) {
            document.querySelectorAll('.search-option').forEach(btn => {
                btn.classList.toggle('active', btn.id === buttonId);
            });
        }
        function showText() {
            document.getElementById("diseaseBox").style.display = "block";
            document.getElementById("additionalFilters").style.display = "block";
            document.getElementById("diseaseCombo").style.display = "none";
            setActiveSearchButton('textBtn');
        }
        function showCombo() {
            document.getElementById("diseaseCombo").style.display = "block";
            document.getElementById("additionalFilters").style.display = "block";
            document.getElementById("diseaseBox").style.display = "none";
            setActiveSearchButton('comboBtn');
        }

        let diseases = [];
        let debounceTimer = null;

        function AddDisease() {
            const textValue  = document.getElementById("diseaseInput").value.trim();
            const comboValue = document.getElementById("diseaseComboBox")?.value;
            let finalValue   = textValue !== "" ? textValue
                             : (comboValue && comboValue !== "") ? comboValue : "";
            if (!finalValue) { alert("Please enter or select a disease"); return; }
            if (diseases.includes(finalValue)) return;
            diseases.push(finalValue);

            let newItem = document.createElement("div");
            newItem.className = "disease-item";
            newItem.innerHTML = `${finalValue}<span onclick="removeDisease('${finalValue.replace(/'/g,"\\'")}',this)">✖</span>`;
            document.getElementById("diseaseList").appendChild(newItem);

            document.getElementById("diseaseInput").value = "";
            document.getElementById("diseaseComboBox").value = "";

            diseaseDescription();
        }

        function handleDiseaseInputKey(event) {
            if (event.key === 'Enter' && diseases.length === 0) {
                event.preventDefault();
                AddDisease();
            }
        }

        function removeDisease(disease, element) {
            diseases = diseases.filter(d => d !== disease);
            element.parentElement.remove();
            diseaseDescription();
        }

        function diseaseDescription() {
            const VegORnonVeg = document.getElementById("VegORnonVeg").value;
            const Age_Group   = document.getElementById("Age_Group").value;
            const allergies   = document.getElementById("allergies").value;

            // Always keep description box hidden
            document.getElementById("diseaseDescription").style.display = "none";

            if (diseases.length > 0 && Age_Group !== '' && VegORnonVeg !== '') {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    autoGenerate(diseases, Age_Group, VegORnonVeg, allergies);
                }, 700);
            } else {
                // Not ready — clear results and hide divider
                document.getElementById("result-divider").style.display = "none";
                document.getElementById("results").innerHTML = '';
            }
        }

        async function autoGenerate(diseaseList, age, pref, allergy) {
            const resultsEl = document.getElementById("results");
            const divider   = document.getElementById("result-divider");

            // Show divider and loading steps
            divider.style.display = "flex";
            resultsEl.innerHTML = `
                <div class="loading-wrap">
                    <p>Analyzing your health profile...</p>
                    <div class="loading-steps">
                        <div class="loading-step"><div class="step-dot">🔍</div><span>Reading your condition(s)</span></div>
                        <div class="loading-step"><div class="step-dot">🧠</div><span>Running AI nutrition rules</span></div>
                        <div class="loading-step"><div class="step-dot">🥗</div><span>Matching safe Sri Lankan meals</span></div>
                        <div class="loading-step"><div class="step-dot">✅</div><span>Preparing your meal plan</span></div>
                    </div>
                </div>`;

            // Scroll so user sees loading animation
            divider.scrollIntoView({ behavior: 'smooth', block: 'start' });

            try {
                const res  = await fetch('recommend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        diseases:   diseaseList,
                        age_group:  age,
                        preference: pref,
                        allergy:    allergy || ''
                    })
                });
                const data = await res.json();
                renderResults(data, resultsEl);
            } catch(err) {
                resultsEl.innerHTML = `
                    <div class="err-box">
                        <span class="err-icon">⚠️</span>
                        <p>Connection error — make sure XAMPP is running.</p>
                    </div>`;
            }
        }

        function renderResults(data, el) {
            if (!data.success) {
                el.innerHTML = `
                    <div class="err-box">
                        <span class="err-icon">🩺</span>
                        <p>${esc(data.message)}</p>
                    </div>`;
                return;
            }

            const ageMap  = {child:'Child',young:'Teenager',adult:'Adult',elderly:'Senior'};
            const prefMap = {veg:'Vegetarian',nonveg:'Non-Vegetarian'};
            let h = '';

            if (data.skipped?.length) {
                h += `<div style="background:#fff8ec;border:1.5px solid #f5a623;border-radius:12px;
                            padding:10px 14px;font-size:0.8rem;color:#7a5c10;font-weight:600;margin-bottom:14px">
                    ℹ️ <strong>"${esc(data.skipped.join(', '))}"</strong> not yet supported —
                    showing plan for supported condition(s) only.
                </div>`;
            }

            // Profile chips
            h += `<div class="profile-bar">`;
            (data.labels||[]).forEach(l =>
                h += `<span class="profile-chip">🦠 <b>Condition:</b> ${esc(l)}</span>`);
            h += `<span class="profile-chip">👤 <b>Age:</b> ${esc(ageMap[data.age]||data.age)}</span>`;
            h += `<span class="profile-chip">🥗 <b>Diet:</b> ${esc(prefMap[data.preference]||data.preference)}</span>`;
            if (data.allergy) h += `<span class="profile-chip">⚠️ <b>Allergy:</b> ${esc(data.allergy)}</span>`;
            h += `</div>`;

            // Meal timeline
            const mealDefs = [
                {icon:'🌅',label:'Breakfast',    nodeBg:'#fff8e8',lc:'#f5a623',val:data.meals?.breakfast},
                {icon:'☀️', label:'Lunch',         nodeBg:'#e8fdf5',lc:'#019C78',val:data.meals?.lunch},
                {icon:'🍎',label:'Evening Snack', nodeBg:'#fdecea',lc:'#e05252',val:data.meals?.snack},
                {icon:'🌙',label:'Dinner',        nodeBg:'#e8f0fd',lc:'#4a7ee8',val:data.meals?.dinner},
                {icon:'🍵',label:'Beverage',      nodeBg:'#f3e8fd',lc:'#9b59b6',val:data.meals?.beverage},
            ].filter(m => m.val);

            if (mealDefs.length) {
                h += `<div class="res-heading">🍽️ Your Daily Meal Plan</div>`;
                h += `<div class="meal-timeline">`;
                mealDefs.forEach(m => {
                    h += `<div class="meal-row">
                        <div class="meal-node" style="background:${m.nodeBg}">${m.icon}</div>
                        <div class="meal-bubble" style="border-left-color:${m.lc}">
                            <div class="meal-time-label" style="color:${m.lc}">${m.label}</div>
                            <div class="meal-name-text">${esc(m.val)}</div>
                        </div>
                    </div>`;
                });
                h += `</div>`;
            }

            // Food guide
            if (data.recommended?.length || data.avoid?.length) {
                h += `<div class="res-heading">🥦 Food Guide</div>`;
                h += `<div class="food-cols">`;
                if (data.recommended?.length) {
                    h += `<div class="food-card fc-green" data-mark="✓">
                        <div class="food-card-hdr">
                            <div class="food-card-ico">✅</div>
                            <span class="food-card-ttl">Highly Recommended</span>
                        </div>
                        <div class="tag-wrap">
                            ${data.recommended.map(r=>`<span class="food-tag"><span class="tag-dot"></span>${esc(r)}</span>`).join('')}
                        </div></div>`;
                }
                if (data.avoid?.length) {
                    h += `<div class="food-card fc-red" data-mark="✕">
                        <div class="food-card-hdr">
                            <div class="food-card-ico">🚫</div>
                            <span class="food-card-ttl">Foods to Avoid</span>
                        </div>
                        <div class="tag-wrap">
                            ${data.avoid.map(a=>`<span class="food-tag"><span class="tag-dot"></span>${esc(a)}</span>`).join('')}
                        </div></div>`;
                }
                h += `</div>`;
            }

            // Nutrition
            if (data.nutrition?.length) {
                h += `<div class="res-heading">📊 Nutrition Targets</div><div class="nutr-box">`;
                data.nutrition.forEach(row => {
                    if (row.disease) h += `<div class="nutr-dis-label">${esc(row.disease)}</div>`;
                    h += `<div class="nutr-grid">`;
                    (row.chips||[]).forEach((chip,i) => {
                        h += `<div class="nutr-pill" style="animation-delay:${i*0.05}s">`;
                        if (chip.label) h += `<span class="nutr-lbl">${esc(chip.label)}</span>`;
                        h += `<span class="nutr-val">${esc(chip.value)}</span></div>`;
                    });
                    h += `</div>`;
                });
                h += `</div>`;
            }

            // Tip
            if (data.tip) {
                h += `<div class="tip-banner">
                    <span class="tip-icon">💡</span>
                    <div>
                        <strong style="display:block;margin-bottom:3px;font-size:0.72rem;
                            text-transform:uppercase;letter-spacing:0.1em;opacity:0.85">Health Tip</strong>
                        ${esc(data.tip)}
                    </div>
                </div>`;
            }

            el.innerHTML = h;
        }

        function esc(s) {
            if (!s) return '';
            return String(s)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    </script>
</head>

<body>
    <nav class="login-btn">
    <?php if (isset($_SESSION['user_name'])): ?>
        <p class="user-name">
            👤<?php echo $_SESSION['user_name']; ?>
        </p>
    <?php elseif(isset($_SESSION['user_email'])): ?>
        <p class="user-name">
            👤 <?php echo substr($_SESSION['user_email'], 0, 6); ?>
        </p>
    <?php else: ?>
        <button type="button" id="signin" onclick="navigateToSignIn()">
        </button>
    <?php endif; ?>
    </nav>

    <div class="container">
        <h1><img class="logo" src="images/logo.png" alt="MedMeal Logo"><span class="medi">Medi</span><span class="meal">ආහාර</span></h1>
        <p>Personalized Sri Lankan meal plans for better health.</p>
        <p class="sinhala">ඔබගේ සෞඛ්‍යයට ගැළපෙන ආහාර සැලසුම්</p>
    </div>

    <div class="form-group">
        <p>How would you like to search?</p>
        <div id="search_options">
            <button type="button" class="search-option" id="comboBtn" onclick="showCombo()">Select from menu</button>
            <button type="button" class="search-option" id="textBtn" onclick="showText()">Text Box</button>
        </div>

        <div id="user-inputs" class="input-box">
            <section id="diseaseBox" class="search-box" style="display: none;">
                <label for="diseaseInput">Search by disease or keyword:</label><br>
                <input id="diseaseInput" name="diseases[]" type="text"
                       placeholder="Enter disease name or symptom..."
                       oninput="diseaseDescription()">
                <button type="button" id="AddDiseaseButton" onclick="AddDisease()"></button>
            </section>

            <section id="diseaseCombo" class="search-box" style="display: none;">
                <label for="diseaseInput">Select from combo box:</label><br>
                <select id="diseaseComboBox" name="diseases[]" onchange="diseaseDescription()">
                    <option value="">--Disease Type--</option>
                    <option value="Diabetes">Diabetes දියවැඩියාව நீரிழிவு நோய்</option>
                    <option value="High Blood Pressure">High Blood Pressure අධි රුධිර පීඩනය</option>
                    <option value="Heart Disease">Heart Disease හෘද රෝග இதய நோய்</option>
                    <option value="Kidney Disease">Kidney Disease (CKD) වකුගඩු රෝග</option>
                    <option value="Cholesterol">Cholesterol කොලෙස්ටරෝල්</option>
                    <optgroup label="── Other Conditions (coming soon) ──">
                        <option value="Dengue" disabled>Osteoporosis (coming soon)</option>
                        <option value="Cancer" disabled>Cancer (coming soon)</option>
                        <option value="Asthma" disabled>Liver Disease (coming soon)</option>
                    </optgroup>
                </select>
                <button type="button" id="AddDiseaseButton" onclick="AddDisease()"></button>
            </section>

            <section class="additionalFilters" id="additionalFilters" style="display: none;">
                <select id="Age_Group" onchange="diseaseDescription()">
                    <option value="">--Age Group--</option>
                    <option value="child">Child ළමයා (0-12)</option>
                    <option value="teen">Teenager තරුණ (13-19)</option>
                    <option value="adult">Adult වැඩිහිටි (20-59)</option>
                    <option value="senior">Senior මහලු (60+)</option>
                </select>

                <select id="VegORnonVeg" onchange="diseaseDescription()">
                    <option value="">--Food Type--</option>
                    <option value="Vegetarian">Vegetarian නිර්මාංශ</option>
                    <option value="Non-Vegetarian">Non-Vegetarian මාංසහාරී</option>
                </select>

                <select id="allergies" onchange="diseaseDescription()">
                    <option value="">-- Select Allergy --</option>
                    <option value="sesame">Sesame (තල)</option>
                    <option value="eggs">Eggs (බිත්තර)</option>
                    <option value="milk">Milk / Dairy (කිරි)</option>
                    <option value="peanuts">Peanuts (රටකජු)</option>
                    <option value="soy">Soy (සෝයා)</option>
                    <option value="wheat">Wheat (තිරිඟු)</option>
                    <option value="fish">fish (මාළු)</option>
                    <option value="tree_nuts">Tree Nuts</option>
                </select>
            </section>
        </div>

    

    </div>

  <div class="results-section"> 
            <div id="diseaseList"></div>

            <!-- Hidden — never shown, kept for compatibility -->
            <div id="diseaseDescription" class="description" style="display:none;"></div>

            <!-- Divider + results live INSIDE form-group so they stay above footer -->
            <div id="result-divider" class="result-divider">Your Meal Plan</div>
            <div id="results"></div>
        </div>

    <footer>
        <p>&copy; 2026 MedMeal. All rights reserved.</p>
    </footer>
</body>
</html>