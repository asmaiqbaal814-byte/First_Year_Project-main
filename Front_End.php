<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedMeal</title>
    <link rel="icon" href="images/titleLogo.png" type="image/png" sizes="19x19">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Noto+Sans+Sinhala&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        /* ── Selection Summary Pill (replaces old ugly description box) ── */
        #selectionSummary {
            display: none;
            margin-top: 18px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #e8fdf5 0%, #d0f5e8 100%);
            border: 1.5px solid #a8e8cf;
            border-radius: 16px;
            font-size: 14px;
            color: #1a6644;
            line-height: 1.7;
            font-weight: 600;
        }
        #selectionSummary .summary-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        #selectionSummary .summary-label {
            font-weight: 700;
            color: #019C78;
            min-width: 80px;
        }
        #selectionSummary .summary-pill {
            background: #019C78;
            color: #fff;
            border-radius: 20px;
            padding: 2px 12px;
            font-size: 13px;
            font-weight: 700;
        }
        #selectionSummary .summary-divider {
            border: none;
            border-top: 1px dashed #a8e8cf;
            margin: 8px 0;
        }
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

        function AddDisease() {
            const textValue  = document.getElementById("diseaseInput").value.trim();
            const comboValue = document.getElementById("diseaseComboBox")?.value;

            let finalValue = "";
            if (textValue !== "") {
                finalValue = textValue;
            } else if (comboValue && comboValue !== "") {
                finalValue = comboValue;
            } else {
                alert("Please enter or select a disease");
                return;
            }

            if (diseases.includes(finalValue)) return;
            diseases.push(finalValue);

            document.getElementById("diseasesHidden").value = diseases.join(',');

            let newItem = document.createElement("div");
            newItem.className = "disease-item";
            newItem.innerHTML = `
                ${finalValue}
                <span onclick="removeDisease('${finalValue}', this)">✖</span>
            `;
            document.getElementById("diseaseList").appendChild(newItem);

            document.getElementById("diseaseInput").value = "";
            document.getElementById("diseaseComboBox").value = "";

            updateSummary();
        }

        function handleDiseaseInputKey(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                AddDisease();
            }
        }

        function removeDisease(disease, element) {
            diseases = diseases.filter(d => d !== disease);
            document.getElementById("diseasesHidden").value = diseases.join(',');
            element.parentElement.remove();
            updateSummary();
        }

        function updateSummary() {
            const VegORnonVeg = document.getElementById("VegORnonVeg").value;
            const Age_Group   = document.getElementById("Age_Group").value;
            const allergies   = document.getElementById("allergies").value;
            const summary     = document.getElementById("selectionSummary");
            const submitBtn   = document.getElementById("submitBtn");

            if (diseases.length > 0 && Age_Group !== '' && VegORnonVeg !== '') {
                const ageLabels = { child: 'Child', teen: 'Teenager', adult: 'Adult', senior: 'Senior' };
                const ageDisplay = ageLabels[Age_Group] || Age_Group;
                const allergyDisplay = allergies ? allergies.charAt(0).toUpperCase() + allergies.slice(1) : 'None';

                summary.innerHTML = `
                    <div class="summary-row">
                        <span class="summary-label">🦠 Condition</span>
                        ${diseases.map(d => `<span class="summary-pill">${d}</span>`).join('')}
                    </div>
                    <hr class="summary-divider">
                    <div class="summary-row">
                        <span class="summary-label">👤 Age</span>
                        <span class="summary-pill">${ageDisplay}</span>
                        <span class="summary-label" style="margin-left:16px;">🥗 Diet</span>
                        <span class="summary-pill">${VegORnonVeg}</span>
                        <span class="summary-label" style="margin-left:16px;">⚠️ Allergy</span>
                        <span class="summary-pill">${allergyDisplay}</span>
                    </div>
                `;
                summary.style.display = "block";
                submitBtn.style.display = "inline-block";
            } else {
                summary.style.display = "none";
                submitBtn.style.display = "none";
            }
        }

        function validateAndSubmit() {
            if (diseases.length === 0) {
                alert("Please add at least one disease.");
                return false;
            }
            if (document.getElementById("Age_Group").value === '') {
                alert("Please select an age group.");
                return false;
            }
            if (document.getElementById("VegORnonVeg").value === '') {
                alert("Please select Vegetarian or Non-Vegetarian.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <nav class="login-btn">
    <?php if (isset($_SESSION['user_name'])): ?>
        <p class="user-name">👤 <?php echo $_SESSION['user_name']; ?></p>
    <?php elseif(isset($_SESSION['user_email'])): ?>
        <p class="user-name">👤 <?php echo substr($_SESSION['user_email'], 0, 6); ?></p>
    <?php else: ?>
        <button type="button" id="signin" onclick="navigateToSignIn()"></button>
    <?php endif; ?>
    </nav>

    <div class="container">
        <h1>
            <img class="logo" src="images/logo.png" alt="MedMeal Logo">
            <span class="medi">Medi</span><span class="meal">ආහාර</span>
        </h1>
        <p>Personalized Sri Lankan meal plans for better health.</p>
        <p class="sinhala">ඔබගේ සෞඛ්‍යයට ගැළපෙන ආහාර සැලසුම්</p>
    </div>

    <!-- ── MAIN FORM — submits to recommend.php ── -->
    <form action="recommend.php" method="POST" onsubmit="return validateAndSubmit()">

        <input type="hidden" name="diseases" id="diseasesHidden" value="">

        <div class="form-group">
            <p>How would you like to search?</p>
            <div id="search_options">
                <button type="button" class="search-option" id="comboBtn" onclick="showCombo()">Select from menu</button>
                <button type="button" class="search-option" id="textBtn"  onclick="showText()">Text Box</button>
            </div>

            <div id="user-inputs" class="input-box">

                <!-- TEXT INPUT MODE -->
                <section id="diseaseBox" class="search-box" style="display: none;">
                    <label for="diseaseInput">Search by disease or keyword:</label><br>
                    <input id="diseaseInput" type="text"
                           placeholder="Enter disease name..."
                           onkeydown="handleDiseaseInputKey(event)">
                    <button type="button" id="AddDiseaseButton" onclick="AddDisease()"></button>
                </section>

                <!-- COMBO MODE -->
                <section id="diseaseCombo" class="search-box" style="display: none;">
                    <label>Select from combo box:</label><br>
                    <select id="diseaseComboBox">
                        <option value="">--Disease Type--</option>
                        <option value="Diabetes">Diabetes දියවැඩියාව நீரிழிவு நோய்</option>
                        <option value="High Blood Pressure">High Blood Pressure අධි රුධිර පීඩනය</option>
                        <option value="Heart Disease">Heart Disease හෘද රෝග இதய நோய்</option>
                        <option value="Kidney Disease">Kidney Disease (CKD) වකුගඩු රෝග</option>
                        <option value="Cholesterol">High Cholesterol කොලෙස්ටරෝල්</option>
                        <optgroup label="── Other Conditions (coming soon) ──">
                            <option value="Dengue" disabled>Dengue (coming soon)</option>
                            <option value="Cancer" disabled>Cancer (coming soon)</option>
                            <option value="Asthma" disabled>Asthma (coming soon)</option>
                        </optgroup>
                    </select>
                    <button type="button" id="AddDiseaseButton" onclick="AddDisease()"></button>
                </section>

                <!-- ADDITIONAL FILTERS -->
                <section class="additionalFilters" id="additionalFilters" style="display: none;">

                    <select id="Age_Group" name="age_group" onchange="updateSummary()">
                        <option value="">--Age Group--</option>
                        <option value="child">Child ළමයා (below 12)</option>
                        <option value="teen">Teenager තරුණ (13-25)</option>
                        <option value="adult" selected>Adult වැඩිහිටි (26-59)</option>
                        <option value="senior">Senior මහලු (60+)</option>
                    </select>

                    <select id="VegORnonVeg" name="preference" onchange="updateSummary()">
                        <option value="">--Food Type--</option>
                        <option value="Vegetarian">Vegetarian නිර්මාංශ</option>
                        <option value="Non-Vegetarian">Non-Vegetarian මාංශ</option>
                    </select>

                    <select id="allergies" name="allergies" onchange="updateSummary()">
                        <option value="">-- No Allergy --</option>
                        <option value="seafood">Seafood (මුහුදු ආහාර)</option>
                        <option value="eggs">Eggs (බිත්තර)</option>
                        <option value="milk">Milk / Dairy (කිරි)</option>
                        <option value="peanuts">Peanuts (රටකජු)</option>
                        <option value="soy">Soy (සෝයා)</option>
                        <option value="coconut">Coconut (පොල්)</option>
                        <option value="spices">Spices (මසාලා)</option>
                    </select>

                </section>
            </div>

            <div id="diseaseList"></div>

            <!-- Clean summary replacing the old ugly description box -->
            <div id="selectionSummary"></div>

            <div style="text-align:center; margin-top: 20px;">
                <button type="submit" id="submitBtn"
                        style="display:none; padding:14px 40px; background:#2d9e6b; color:#fff;
                               font-family:Nunito,sans-serif; font-size:1rem; font-weight:800;
                               border:none; border-radius:999px; cursor:pointer;
                               box-shadow:0 4px 16px rgba(45,158,107,0.3);">
                    🍽️ Get My Meal Plan
                </button>
            </div>

        </div>

    </form>

    <footer>
        <p>&copy; 2026 MedMeal. All rights reserved.</p>
    </footer>
</body>
</html>