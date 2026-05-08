% =============================================
% FOOD RECOMMENDATION SYSTEM FOR DISEASE PATIENTS
% Sri Lankan Context - Final Complete Version
% Features: Diseases, Age Group, Veg/Non-Veg, Allergies,
%           Nutrition Info, Age-Based Tips, Highly Recommended Foods
% Multi-disease: Meals are INTERSECTION-SAFE (safe for ALL selected diseases)
% =============================================

:- use_module(library(lists)).
:- use_module(library(random)).

% ================================
% DISEASES
% ================================
disease(diabetes).
disease(hypertension).
disease(heart_disease).
disease(ckd).
disease(cholesterol).

% ================================
% AGE GROUPS
% ================================
age_group(child).
age_group(young).
age_group(adult).
age_group(elderly).

% ================================
% DIET PREFERENCE
% ================================
preference(veg).
preference(nonveg).

% ================================
% ALLERGIES
% ================================
allergy(milk).
allergy(egg).
allergy(peanut).
allergy(tree_nut).
allergy(fish).
allergy(shellfish).
allergy(soy).
allergy(wheat).
allergy(sesame).

% =============================================
% MEAL SAFETY TAGS
% Format: meal(Name, Preference, AllergyTags, [SafeForDiseases])
% For multi-disease: meal must explicitly cover ALL selected diseases
% =============================================

% ================================
% BREAKFAST MEALS
% ================================

% --- diabetes ---
breakfast_meal('Kurakkan Roti with Dhal Curry and Gotu Kola Mallum', veg, [], [diabetes]).
breakfast_meal('Oats Porridge with Papaya (no sugar, no milk)', veg, [], [diabetes]).
breakfast_meal('Jackfruit (Polos) Curry with Whole Wheat Roti', veg, [wheat], [diabetes]).
breakfast_meal('Tomato Sambol with Red Rice Hoppers', veg, [], [diabetes]).
breakfast_meal('Kankun Mallum with Kurakkan Roti', veg, [], [diabetes]).
breakfast_meal('Bitter Gourd Curry with Atta Roti', veg, [wheat], [diabetes]).
breakfast_meal('Red Rice String Hoppers with Coconut Sambol and Dhal', veg, [], [diabetes]).
breakfast_meal('Mashed Sweet Potato with Spinach Stir Fry', veg, [], [diabetes]).
breakfast_meal('Boiled Cassava with Dhal Curry and Gotu Kola Mallum', veg, [], [diabetes]).
breakfast_meal('Skinless Chicken Curry with Red Rice Congee', nonveg, [], [diabetes]).
breakfast_meal('Egg White Omelette with Spinach and Kurakkan Roti', nonveg, [egg], [diabetes]).
breakfast_meal('Red Rice String Hoppers with Grilled Fish and Greens', nonveg, [fish], [diabetes]).
breakfast_meal('Mashed Sweet Potato with Sardine Curry (low oil)', nonveg, [fish], [diabetes]).

% --- hypertension ---
breakfast_meal('Oats Porridge with Banana and Chia Seeds (no salt)', veg, [], [hypertension]).
breakfast_meal('Kurakkan Roti with Dhal and Carrot Curry (no salt)', veg, [], [hypertension]).
breakfast_meal('Red Rice with Gotu Kola Mallum and Dhal Curry (no salt)', veg, [], [hypertension]).
breakfast_meal('Kurakkan Porridge with Papaya Slices', veg, [], [hypertension]).
breakfast_meal('Beetroot Curry with Atta Roti (no salt)', veg, [wheat], [hypertension]).
breakfast_meal('Low-Fat Curd with Red Rice Roti and Coconut Mint Chutney', veg, [milk], [hypertension]).
breakfast_meal('Boiled Cassava with Low-Salt Coconut Sambol', veg, [], [hypertension]).
breakfast_meal('Herb Grilled Chicken with Red Rice Roti and Avocado Salad', nonveg, [], [hypertension]).
breakfast_meal('Boiled Egg White with Lettuce and Tomato (no salt)', nonveg, [egg], [hypertension]).
breakfast_meal('Fish Curry (low salt) with String Hoppers (red rice)', nonveg, [fish], [hypertension]).
breakfast_meal('Boiled Plantain with Low-Salt Spinach and Egg Salad', nonveg, [egg], [hypertension]).

% --- heart_disease ---
breakfast_meal('Oats Porridge with Apple and Walnuts (no sugar)', veg, [tree_nut], [heart_disease]).
breakfast_meal('Kurakkan Roti with Dhal Curry and Spinach Mallum', veg, [], [heart_disease]).
breakfast_meal('Atta Roti with Young Jackfruit Curry and Green Gram', veg, [wheat], [heart_disease]).
breakfast_meal('Red Rice with Gotu Kola Mallum and Dhal (low oil)', veg, [], [heart_disease]).
breakfast_meal('Steamed Idli with Sambar and Coconut Chutney (low oil)', veg, [], [heart_disease]).
breakfast_meal('Red Rice Hoppers with Grilled Fish Curry and Greens', nonveg, [fish], [heart_disease]).
breakfast_meal('Herb Grilled Chicken with Spinach and Whole Wheat Toast', nonveg, [wheat], [heart_disease]).
breakfast_meal('Oats Bowl with Grilled Chicken Strips and Fresh Salad', nonveg, [], [heart_disease]).
breakfast_meal('Whole Wheat Sandwich with Tuna, Lettuce and Cucumber', nonveg, [fish, wheat], [heart_disease]).
breakfast_meal('Grilled Mackerel with Red Rice and Leafy Green Mallum', nonveg, [fish], [heart_disease]).

% --- ckd ---
breakfast_meal('Plain Oats with Apple Slices (no salt, no milk)', veg, [], [ckd]).
breakfast_meal('White Rice Congee with Cabbage and Carrot (low potassium)', veg, [], [ckd]).
breakfast_meal('White Bread Toast with Cucumber and Lettuce', veg, [wheat], [ckd]).
breakfast_meal('Rice Flour Pancakes with Apple Compote (no sugar)', veg, [], [ckd]).
breakfast_meal('Steamed White Rice with Boiled Cabbage and Carrot', veg, [], [ckd]).
breakfast_meal('Plain Semolina Porridge with White Bread (no salt)', veg, [wheat], [ckd]).
breakfast_meal('Boiled Cassava (well-boiled, water discarded) with Cabbage', veg, [], [ckd]).
breakfast_meal('Egg White Omelette with Lettuce and White Toast', nonveg, [egg, wheat], [ckd]).
breakfast_meal('Rice Congee with Egg White and Cabbage Stir Fry (no salt)', nonveg, [egg], [ckd]).
breakfast_meal('Boiled White Rice with Steamed Carrot and Cucumber', nonveg, [], [ckd]).

% --- cholesterol ---
breakfast_meal('Oats Porridge with Guava (no sugar)', veg, [], [cholesterol]).
breakfast_meal('Kurakkan Roti with Dhal and Eggplant (no coconut oil)', veg, [], [cholesterol]).
breakfast_meal('Chia Seeds, Low-Fat Milk and Oats Pudding', veg, [milk], [cholesterol]).
breakfast_meal('Cooked Mushrooms, Spinach and Tomato on Wholegrain Toast', veg, [wheat], [cholesterol]).
breakfast_meal('Red Rice String Hoppers with Dhal and Mukunuwenna Mallum', veg, [], [cholesterol]).
breakfast_meal('Barley Porridge with Papaya Slices', veg, [], [cholesterol]).
breakfast_meal('Red Rice with Fish and Beans Curry (no coconut milk)', nonveg, [fish], [cholesterol]).
breakfast_meal('Fish Curry with Whole Wheat Roti (no coconut milk)', nonveg, [fish, wheat], [cholesterol]).
breakfast_meal('Grilled Chicken with Oats and Salad', nonveg, [], [cholesterol]).
breakfast_meal('Steamed Fish with Red Rice and Leafy Green Mallum', nonveg, [fish], [cholesterol]).

% --- MULTI: diabetes + hypertension ---
breakfast_meal('Kurakkan Roti with Dhal (no salt) and Gotu Kola Mallum', veg, [], [diabetes, hypertension]).
breakfast_meal('Oats Porridge with Papaya (no sugar, no salt)', veg, [], [diabetes, hypertension]).
breakfast_meal('Red Rice String Hoppers with Dhal and Beetroot Curry (no salt)', veg, [], [diabetes, hypertension]).
breakfast_meal('Bitter Gourd and Carrot Curry with Kurakkan Roti (no salt)', veg, [], [diabetes, hypertension]).
breakfast_meal('Boiled Cassava with Dhal (no salt) and Gotu Kola', veg, [], [diabetes, hypertension]).
breakfast_meal('Herb Grilled Chicken with Red Rice Congee (no salt)', nonveg, [], [diabetes, hypertension]).
breakfast_meal('Grilled Fish with Red Rice Hoppers (no salt, low oil)', nonveg, [fish], [diabetes, hypertension]).
breakfast_meal('Egg White Omelette with Spinach (no salt) and Kurakkan Roti', nonveg, [egg], [diabetes, hypertension]).

% --- MULTI: diabetes + heart_disease ---
breakfast_meal('Kurakkan Roti with Dhal and Spinach (low oil, no sugar)', veg, [], [diabetes, heart_disease]).
breakfast_meal('Oats with Papaya and Chia Seeds (no sugar, low oil)', veg, [], [diabetes, heart_disease]).
breakfast_meal('Red Rice Congee with Gotu Kola Mallum and Dhal', veg, [], [diabetes, heart_disease]).
breakfast_meal('Grilled Fish with Red Rice and Mukunuwenna Mallum', nonveg, [fish], [diabetes, heart_disease]).
breakfast_meal('Oats Bowl with Grilled Chicken and Fresh Salad (no sugar)', nonveg, [], [diabetes, heart_disease]).
breakfast_meal('Herb Grilled Skinless Chicken with Bitter Gourd and Kurakkan Roti', nonveg, [], [diabetes, heart_disease]).

% --- MULTI: hypertension + heart_disease ---
breakfast_meal('Oats Porridge with Banana (no salt, no added sugar)', veg, [], [hypertension, heart_disease]).
breakfast_meal('Kurakkan Roti with Dhal and Carrot (no salt, low oil)', veg, [], [hypertension, heart_disease]).
breakfast_meal('Red Rice with Spinach Mallum and Dhal (no salt, low oil)', veg, [], [hypertension, heart_disease]).
breakfast_meal('Grilled Fish with Red Rice Hoppers (no salt, low oil)', nonveg, [fish], [hypertension, heart_disease]).
breakfast_meal('Herb Grilled Chicken with Avocado Salad and Red Rice Roti (no salt)', nonveg, [], [hypertension, heart_disease]).

% --- MULTI: diabetes + cholesterol ---
breakfast_meal('Oats Porridge with Guava (no sugar, low fat)', veg, [], [diabetes, cholesterol]).
breakfast_meal('Kurakkan Roti with Dhal and Eggplant (no sugar, no coconut oil)', veg, [], [diabetes, cholesterol]).
breakfast_meal('Red Rice String Hoppers with Beans Curry and Mukunuwenna (low fat)', veg, [], [diabetes, cholesterol]).
breakfast_meal('Grilled Fish with Red Rice and Leafy Greens (low fat, no sugar)', nonveg, [fish], [diabetes, cholesterol]).

% --- MULTI: hypertension + cholesterol ---
breakfast_meal('Oats Porridge with Papaya (no salt, low fat)', veg, [], [hypertension, cholesterol]).
breakfast_meal('Kurakkan Roti with Dhal (no salt, no coconut oil) and Carrot Curry', veg, [], [hypertension, cholesterol]).
breakfast_meal('Grilled Fish with Red Rice and Spinach (no salt, low fat)', nonveg, [fish], [hypertension, cholesterol]).
breakfast_meal('Herb Grilled Chicken with Oats and Salad (no salt)', nonveg, [], [hypertension, cholesterol]).

% --- MULTI: heart_disease + cholesterol ---
breakfast_meal('Oats Porridge with Apple and Chia Seeds (low fat, no sugar)', veg, [], [heart_disease, cholesterol]).
breakfast_meal('Kurakkan Roti with Dhal and Spinach (low oil, low fat)', veg, [], [heart_disease, cholesterol]).
breakfast_meal('Grilled Mackerel with Red Rice and Mukunuwenna (low fat, low oil)', nonveg, [fish], [heart_disease, cholesterol]).

% --- MULTI: diabetes + hypertension + heart_disease ---
breakfast_meal('Kurakkan Roti with Dhal (no salt, no sugar, low oil) and Gotu Kola', veg, [], [diabetes, hypertension, heart_disease]).
breakfast_meal('Oats Porridge with Papaya (no sugar, no salt, low oil)', veg, [], [diabetes, hypertension, heart_disease]).
breakfast_meal('Red Rice String Hoppers with Dhal and Mukunuwenna (no salt, no sugar)', veg, [], [diabetes, hypertension, heart_disease]).
breakfast_meal('Herb Grilled Fish with Red Rice Congee (no salt, no sugar, low oil)', nonveg, [fish], [diabetes, hypertension, heart_disease]).
breakfast_meal('Herb Grilled Chicken with Bitter Gourd and Kurakkan Roti (no salt, no sugar)', nonveg, [], [diabetes, hypertension, heart_disease]).

% --- MULTI: diabetes + ckd ---
breakfast_meal('Plain White Rice Congee with Boiled Cabbage (no salt, low potassium)', veg, [], [diabetes, ckd]).
breakfast_meal('Rice Flour Pancakes with Apple (no sugar, no salt)', veg, [], [diabetes, ckd]).
breakfast_meal('Boiled Cassava (water discarded) with Cabbage and Lettuce (no salt, no sugar)', veg, [], [diabetes, ckd]).
breakfast_meal('Egg White Omelette with Cabbage and Lettuce (no salt, no sugar)', nonveg, [egg], [diabetes, ckd]).

% ================================
% LUNCH / DINNER MEALS
% ================================

% --- diabetes ---
lunch_dinner_meal('Red Rice + Dhal Curry + Jackfruit (Polos) Curry + Gotu Kola Mallum', veg, [], [diabetes]).
lunch_dinner_meal('Kurakkan Roti + Lentils + Eggplant Curry + Beans', veg, [wheat], [diabetes]).
lunch_dinner_meal('Brown Rice + Dhal + Bitter Gourd + Kankun Mallum', veg, [], [diabetes]).
lunch_dinner_meal('Atta Roti + Mixed Vegetable Curry + Mukunuwenna + Lentils', veg, [wheat], [diabetes]).
lunch_dinner_meal('Red Rice + Tempeh Curry + Bandakka + Spinach Mallum', veg, [soy], [diabetes]).
lunch_dinner_meal('Boiled Cassava + Dhal Curry + Bitter Gourd + Kankun Mallum', veg, [], [diabetes]).
lunch_dinner_meal('Red Rice + Fish Curry (less oil) + Bandakka + Mukunuwenna', nonveg, [fish], [diabetes]).
lunch_dinner_meal('Brown Rice + Skinless Chicken Curry + Bitter Gourd + Dhal', nonveg, [], [diabetes]).
lunch_dinner_meal('Red Rice + Lentils + Jackfruit + Spinach Mallum', nonveg, [], [diabetes]).
lunch_dinner_meal('Red Rice + Grilled Fish + Beans Curry + Gotu Kola', nonveg, [fish], [diabetes]).
lunch_dinner_meal('Kurakkan Roti + Dhal + Steamed Vegetables + Kankun', nonveg, [], [diabetes]).

% --- hypertension ---
lunch_dinner_meal('Red Rice + Dhal (no salt) + Papaya Sambol + Gotu Kola', veg, [], [hypertension]).
lunch_dinner_meal('Kurakkan Roti + Beans + Carrot + Spinach (no salt)', veg, [], [hypertension]).
lunch_dinner_meal('Red Rice + Beetroot Curry + Lentils + Mukunuwenna (no salt)', veg, [], [hypertension]).
lunch_dinner_meal('Brown Rice + Tempeh + Carrot + Gotu Kola (no salt)', veg, [soy], [hypertension]).
lunch_dinner_meal('Atta Roti + Dhal + Cabbage Stir Fry + Cucumber Sambol (no salt)', veg, [wheat], [hypertension]).
lunch_dinner_meal('Boiled Cassava + Lentils + Carrot + Gotu Kola (no salt)', veg, [], [hypertension]).
lunch_dinner_meal('Red Rice + Fish Curry (low salt) + Beetroot + Greens', nonveg, [fish], [hypertension]).
lunch_dinner_meal('Brown Rice + Grilled Chicken + Carrot + Spinach (no salt)', nonveg, [], [hypertension]).
lunch_dinner_meal('Red Rice + Steamed Fish + Papaya Sambol + Mukunuwenna (no salt)', nonveg, [fish], [hypertension]).
lunch_dinner_meal('Red Rice + Herb Chicken + Cucumber + Gotu Kola (no salt)', nonveg, [], [hypertension]).
lunch_dinner_meal('Kurakkan Roti + Boiled Egg White + Lettuce + Tomato (no salt)', nonveg, [egg], [hypertension]).

% --- heart_disease ---
lunch_dinner_meal('Red Rice + Dhal + Spinach Mallum + Beans Curry', veg, [], [heart_disease]).
lunch_dinner_meal('Brown Rice + Jackfruit Curry + Mukunuwenna + Lentils (low oil)', veg, [], [heart_disease]).
lunch_dinner_meal('Kurakkan Roti + Green Gram Curry + Spinach + Carrot (low oil)', veg, [], [heart_disease]).
lunch_dinner_meal('Red Rice + Tempeh Curry + Bitter Gourd + Gotu Kola (low oil)', veg, [soy], [heart_disease]).
lunch_dinner_meal('Atta Roti + Dhal + Beetroot Curry + Spinach (low oil)', veg, [wheat], [heart_disease]).
lunch_dinner_meal('Red Rice + Grilled Fish + Mixed Vegetables + Gotu Kola', nonveg, [fish], [heart_disease]).
lunch_dinner_meal('Brown Rice + Steamed Fish + Spinach Mallum + Dhal', nonveg, [fish], [heart_disease]).
lunch_dinner_meal('Red Rice + Herb Grilled Chicken + Beans + Mukunuwenna (low oil)', nonveg, [], [heart_disease]).
lunch_dinner_meal('Whole Wheat Roti + Tuna Curry + Cabbage + Carrot (low oil)', nonveg, [fish, wheat], [heart_disease]).
lunch_dinner_meal('Brown Rice + Mackerel Curry (low oil) + Bitter Gourd + Spinach', nonveg, [fish], [heart_disease]).

% --- ckd ---
lunch_dinner_meal('White Rice + Small Dhal + Cabbage + Cucumber (low potassium, no salt)', veg, [], [ckd]).
lunch_dinner_meal('White Rice + Boiled Carrot + Lettuce Salad + Small Cabbage Curry', veg, [], [ckd]).
lunch_dinner_meal('White Rice + Cauliflower Stir Fry + Cucumber + Cabbage (low salt)', veg, [], [ckd]).
lunch_dinner_meal('White Rice + Plain Dhal (small) + Boiled Cabbage + White Bread', veg, [wheat], [ckd]).
lunch_dinner_meal('Plain Semolina + Steamed Carrot and Cabbage (no salt)', veg, [], [ckd]).
lunch_dinner_meal('Boiled Cassava (water discarded) + Cabbage + Cucumber (no salt)', veg, [], [ckd]).
lunch_dinner_meal('White Rice + Egg White + Cucumber + Boiled Cabbage (no salt)', nonveg, [egg], [ckd]).
lunch_dinner_meal('White Rice + Steamed Chicken (small portion) + Lettuce + Carrot', nonveg, [], [ckd]).
lunch_dinner_meal('White Rice + Boiled Fish (small portion) + Cabbage + Cucumber', nonveg, [fish], [ckd]).
lunch_dinner_meal('Plain Rice + Egg White Stir Fry + Boiled Carrot + Cabbage', nonveg, [egg], [ckd]).
lunch_dinner_meal('White Rice + Chicken Broth (clear) + Steamed Cabbage + Lettuce', nonveg, [], [ckd]).

% --- cholesterol ---
lunch_dinner_meal('Red Rice + Dhal + Eggplant Curry (no coconut milk)', veg, [], [cholesterol]).
lunch_dinner_meal('Brown Rice + Lentils + Mukunuwenna + Bitter Gourd (low fat)', veg, [], [cholesterol]).
lunch_dinner_meal('Kurakkan Roti + Green Gram + Spinach + Carrot (no coconut oil)', veg, [], [cholesterol]).
lunch_dinner_meal('Red Rice + Tempeh Curry + Cabbage + Gotu Kola (low fat)', veg, [soy], [cholesterol]).
lunch_dinner_meal('Oat-Based Roti + Dhal + Eggplant + Mukunuwenna (low fat)', veg, [wheat], [cholesterol]).
lunch_dinner_meal('Red Rice + Grilled Fish + Bandakka + Leafy Greens (no coconut milk)', nonveg, [fish], [cholesterol]).
lunch_dinner_meal('Brown Rice + Steamed Fish + Mukunuwenna + Dhal (low fat)', nonveg, [fish], [cholesterol]).
lunch_dinner_meal('Red Rice + Grilled Chicken + Beans + Spinach (no coconut oil)', nonveg, [], [cholesterol]).
lunch_dinner_meal('Whole Wheat Roti + Tuna + Cabbage + Carrot (low fat)', nonveg, [fish, wheat], [cholesterol]).
lunch_dinner_meal('Brown Rice + Mackerel (grilled) + Bitter Gourd + Gotu Kola', nonveg, [fish], [cholesterol]).

% --- MULTI: diabetes + hypertension ---
lunch_dinner_meal('Red Rice + Dhal (no salt) + Bitter Gourd + Gotu Kola Mallum', veg, [], [diabetes, hypertension]).
lunch_dinner_meal('Kurakkan Roti + Lentils + Carrot Curry + Spinach (no salt)', veg, [], [diabetes, hypertension]).
lunch_dinner_meal('Brown Rice + Dhal (no salt) + Papaya Sambol + Mukunuwenna', veg, [], [diabetes, hypertension]).
lunch_dinner_meal('Boiled Cassava + Dhal (no salt) + Bitter Gourd + Mukunuwenna', veg, [], [diabetes, hypertension]).
lunch_dinner_meal('Red Rice + Fish Curry (low salt, low oil) + Bitter Gourd + Greens', nonveg, [fish], [diabetes, hypertension]).
lunch_dinner_meal('Brown Rice + Herb Grilled Chicken + Carrot + Gotu Kola (no salt)', nonveg, [], [diabetes, hypertension]).

% --- MULTI: diabetes + heart_disease ---
lunch_dinner_meal('Red Rice + Dhal + Spinach Mallum + Bitter Gourd (low oil, no sugar)', veg, [], [diabetes, heart_disease]).
lunch_dinner_meal('Brown Rice + Green Gram + Jackfruit Curry + Mukunuwenna (low oil)', veg, [], [diabetes, heart_disease]).
lunch_dinner_meal('Kurakkan Roti + Lentils + Eggplant + Kankun (low oil)', veg, [], [diabetes, heart_disease]).
lunch_dinner_meal('Red Rice + Grilled Fish + Beans + Gotu Kola (low oil, no sugar)', nonveg, [fish], [diabetes, heart_disease]).
lunch_dinner_meal('Brown Rice + Herb Chicken + Bitter Gourd + Spinach (low oil)', nonveg, [], [diabetes, heart_disease]).

% --- MULTI: hypertension + heart_disease ---
lunch_dinner_meal('Red Rice + Dhal (no salt, low oil) + Spinach + Carrot', veg, [], [hypertension, heart_disease]).
lunch_dinner_meal('Brown Rice + Lentils (no salt) + Beetroot + Gotu Kola (low oil)', veg, [], [hypertension, heart_disease]).
lunch_dinner_meal('Red Rice + Grilled Fish (no salt, low oil) + Mukunuwenna + Carrot', nonveg, [fish], [hypertension, heart_disease]).
lunch_dinner_meal('Brown Rice + Herb Chicken (no salt, low oil) + Spinach + Beans', nonveg, [], [hypertension, heart_disease]).

% --- MULTI: diabetes + cholesterol ---
lunch_dinner_meal('Red Rice + Dhal + Bitter Gourd + Mukunuwenna (low fat, no sugar)', veg, [], [diabetes, cholesterol]).
lunch_dinner_meal('Brown Rice + Green Gram + Eggplant + Gotu Kola (low fat)', veg, [], [diabetes, cholesterol]).
lunch_dinner_meal('Red Rice + Grilled Fish + Leafy Greens + Beans (low fat, no sugar)', nonveg, [fish], [diabetes, cholesterol]).
lunch_dinner_meal('Brown Rice + Grilled Chicken + Spinach + Bitter Gourd (low fat)', nonveg, [], [diabetes, cholesterol]).

% --- MULTI: hypertension + cholesterol ---
lunch_dinner_meal('Red Rice + Dhal (no salt, no coconut oil) + Spinach + Carrot', veg, [], [hypertension, cholesterol]).
lunch_dinner_meal('Brown Rice + Lentils (no salt, low fat) + Mukunuwenna + Beetroot', veg, [], [hypertension, cholesterol]).
lunch_dinner_meal('Red Rice + Grilled Fish (no salt, low fat) + Spinach + Beans', nonveg, [fish], [hypertension, cholesterol]).
lunch_dinner_meal('Brown Rice + Herb Chicken (no salt, no coconut oil) + Greens', nonveg, [], [hypertension, cholesterol]).

% --- MULTI: heart_disease + cholesterol ---
lunch_dinner_meal('Red Rice + Dhal + Spinach + Bitter Gourd (low oil, low fat)', veg, [], [heart_disease, cholesterol]).
lunch_dinner_meal('Brown Rice + Green Gram + Eggplant + Mukunuwenna (low fat, low oil)', veg, [], [heart_disease, cholesterol]).
lunch_dinner_meal('Red Rice + Grilled Mackerel + Leafy Greens + Beans (low fat, low oil)', nonveg, [fish], [heart_disease, cholesterol]).
lunch_dinner_meal('Brown Rice + Herb Grilled Chicken + Bitter Gourd + Gotu Kola (low fat)', nonveg, [], [heart_disease, cholesterol]).

% --- MULTI: diabetes + hypertension + heart_disease ---
lunch_dinner_meal('Red Rice + Dhal (no salt, no sugar, low oil) + Bitter Gourd + Gotu Kola', veg, [], [diabetes, hypertension, heart_disease]).
lunch_dinner_meal('Brown Rice + Lentils (no salt, no sugar) + Spinach + Mukunuwenna (low oil)', veg, [], [diabetes, hypertension, heart_disease]).
lunch_dinner_meal('Red Rice + Grilled Fish (no salt, low oil, no sugar) + Beans + Greens', nonveg, [fish], [diabetes, hypertension, heart_disease]).
lunch_dinner_meal('Brown Rice + Herb Chicken (no salt, low oil) + Bitter Gourd + Spinach', nonveg, [], [diabetes, hypertension, heart_disease]).

% --- MULTI: diabetes + ckd ---
lunch_dinner_meal('White Rice + Small Dhal + Cabbage + Cucumber (no salt, low potassium, no sugar)', veg, [], [diabetes, ckd]).
lunch_dinner_meal('White Rice + Boiled Carrot + Cabbage + Lettuce (no salt, no sugar)', veg, [], [diabetes, ckd]).
lunch_dinner_meal('Boiled Cassava (water discarded) + Cabbage + Cucumber (no salt, no sugar)', veg, [], [diabetes, ckd]).
lunch_dinner_meal('White Rice + Egg White + Cabbage + Lettuce (no salt, no sugar)', nonveg, [egg], [diabetes, ckd]).

% ================================
% EVENING SNACKS (5 per disease + multi)
% ================================

% diabetes
evening_snack('Guava or Papaya Slices (no sugar)', [], [diabetes]).
evening_snack('Roasted Chickpeas (small portion, no salt)', [], [diabetes]).
evening_snack('Apple with Skin', [], [diabetes]).
evening_snack('Cucumber and Carrot Sticks', [], [diabetes]).
evening_snack('Handful of Almonds (unsalted)', [tree_nut], [diabetes]).

% hypertension
evening_snack('Banana (small, no added salt)', [], [hypertension]).
evening_snack('Cucumber and Celery Sticks', [], [hypertension]).
evening_snack('Unsalted Roasted Pumpkin Seeds', [], [hypertension]).
evening_snack('Papaya Slices', [], [hypertension]).
evening_snack('Low-Fat Yogurt (no sugar, no salt)', [milk], [hypertension]).

% heart_disease
evening_snack('Handful of Walnuts (unsalted)', [tree_nut], [heart_disease]).
evening_snack('Guava Slices', [], [heart_disease]).
evening_snack('Oat Biscuits (no sugar, no fat)', [], [heart_disease]).
evening_snack('Apple with Skin', [], [heart_disease]).
evening_snack('Watermelon Slices (small portion)', [], [heart_disease]).

% ckd
evening_snack('Apple Slices (low potassium)', [], [ckd]).
evening_snack('White Rice Crackers (no salt)', [], [ckd]).
evening_snack('Pear Slices (low potassium)', [], [ckd]).
evening_snack('Grapes (small portion, low potassium)', [], [ckd]).
evening_snack('Plain Rice Cake (no salt)', [], [ckd]).

% cholesterol
evening_snack('Oat Biscuits (no sugar, no butter)', [], [cholesterol]).
evening_snack('Papaya Slices', [], [cholesterol]).
evening_snack('Handful of Flaxseeds mixed with Water', [], [cholesterol]).
evening_snack('Guava Slices', [], [cholesterol]).
evening_snack('Plain Popcorn (no butter, no salt)', [], [cholesterol]).

% multi snacks
evening_snack('Apple Slices (no sugar, no salt)', [], [diabetes, hypertension]).
evening_snack('Cucumber and Carrot Sticks (no salt)', [], [diabetes, hypertension]).
evening_snack('Guava Slices (no sugar, no salt)', [], [diabetes, hypertension]).
evening_snack('Apple with Skin (no sugar)', [], [diabetes, heart_disease]).
evening_snack('Guava Slices (no sugar, low fat)', [], [diabetes, heart_disease]).
evening_snack('Cucumber Sticks', [], [diabetes, heart_disease]).
evening_snack('Papaya Slices (no salt, no sugar)', [], [hypertension, heart_disease]).
evening_snack('Guava Slices (no salt, no fat)', [], [hypertension, heart_disease]).
evening_snack('Apple Slices (no sugar, low potassium)', [], [diabetes, ckd]).
evening_snack('Plain Rice Crackers (no salt, no sugar)', [], [diabetes, ckd]).
evening_snack('Guava Slices (no fat, no sugar)', [], [diabetes, cholesterol]).
evening_snack('Oat Biscuits (no sugar, no fat)', [], [diabetes, cholesterol]).
evening_snack('Papaya Slices (no salt, no fat)', [], [hypertension, cholesterol]).
evening_snack('Plain Popcorn (no butter, no salt)', [], [hypertension, cholesterol]).
evening_snack('Oat Biscuits (no sugar, no fat, no salt)', [], [heart_disease, cholesterol]).
evening_snack('Apple Slices (no fat, no sugar)', [], [heart_disease, cholesterol]).
evening_snack('Guava or Apple Slices (no salt, no sugar, low fat)', [], [diabetes, hypertension, heart_disease]).
evening_snack('Cucumber and Carrot Sticks (no salt, no sugar, low fat)', [], [diabetes, hypertension, heart_disease]).

% ================================
% HERBAL BEVERAGES
% ================================
herbal_beverage('Cinnamon Tea (no sugar)', [], [diabetes]).
herbal_beverage('Gotu Kola Tea', [], [diabetes]).
herbal_beverage('Ginger Tea (no sugar)', [], [diabetes]).
herbal_beverage('Bitter Gourd Juice (diluted, no sugar)', [], [diabetes]).
herbal_beverage('Fenugreek Water (overnight soaked)', [], [diabetes]).
herbal_beverage('Hibiscus Tea (no sugar, no salt)', [], [hypertension]).
herbal_beverage('Cucumber-Infused Water', [], [hypertension]).
herbal_beverage('Gotu Kola Juice (no salt)', [], [hypertension]).
herbal_beverage('Celery Juice (fresh, no salt)', [], [hypertension]).
herbal_beverage('Coconut Water (fresh, unsweetened)', [], [hypertension]).
herbal_beverage('Green Tea (no sugar)', [], [heart_disease]).
herbal_beverage('Hibiscus Tea (no sugar)', [], [heart_disease]).
herbal_beverage('Flaxseed Water', [], [heart_disease]).
herbal_beverage('Gotu Kola Juice (no sugar)', [], [heart_disease]).
herbal_beverage('Pomegranate Juice (fresh, no sugar)', [], [heart_disease]).
herbal_beverage('Plain Warm Water with Lemon (no salt)', [], [ckd]).
herbal_beverage('Plain Warm Water', [], [ckd]).
herbal_beverage('Apple Juice (diluted, no sugar)', [], [ckd]).
herbal_beverage('Cranberry Juice (unsweetened, diluted)', [], [ckd]).
herbal_beverage('Plain Herbal Tea (no potassium herbs, no salt)', [], [ckd]).
herbal_beverage('Green Tea (no sugar)', [], [cholesterol]).
herbal_beverage('Oat Water (plain, no sugar)', [], [cholesterol]).
herbal_beverage('Flaxseed Water', [], [cholesterol]).
herbal_beverage('Ginger Lemon Herbal Tea (no sugar)', [], [cholesterol]).
herbal_beverage('Barley Water (unsweetened)', [], [cholesterol]).
herbal_beverage('Gotu Kola Tea (no sugar, no salt)', [], [diabetes, hypertension]).
herbal_beverage('Cucumber-Infused Water (no sugar, no salt)', [], [diabetes, hypertension]).
herbal_beverage('Green Tea (no sugar, no salt)', [], [diabetes, hypertension, heart_disease, cholesterol]).
herbal_beverage('Plain Warm Water with Lemon (no sugar, no salt)', [], [diabetes, hypertension, heart_disease, ckd, cholesterol]).
herbal_beverage('Gotu Kola Tea (no sugar, no salt, low potassium)', [], [diabetes, hypertension, heart_disease]).

% ================================
% HIGHLY RECOMMENDED FOODS
% Matches "View foods patients should take" use case (Figure 3.1)
% ================================
recommended_food(diabetes, 'Kurakkan (Finger Millet) - low glycemic grain, controls blood sugar').
recommended_food(diabetes, 'Bitter Gourd (Karawila) - natural blood sugar reducer').
recommended_food(diabetes, 'Mukunuwenna - high fiber leafy green, slows sugar absorption').
recommended_food(diabetes, 'Gotu Kola - anti-inflammatory, supports insulin function').
recommended_food(diabetes, 'Cinnamon (Kurundu) - helps regulate blood glucose levels').
recommended_food(diabetes, 'Cassava (Manioc, boiled) - moderate GI, filling local staple').
recommended_food(diabetes, 'Red Rice - lower GI than white rice, high fiber').

recommended_food(hypertension, 'Beetroot - naturally lowers blood pressure').
recommended_food(hypertension, 'Banana - high potassium, balances sodium levels').
recommended_food(hypertension, 'Gotu Kola - supports blood vessel health').
recommended_food(hypertension, 'Garlic - natural ACE inhibitor, reduces blood pressure').
recommended_food(hypertension, 'Hibiscus Tea - clinically shown to reduce blood pressure').
recommended_food(hypertension, 'Cucumber - hydrating, low sodium, blood pressure friendly').
recommended_food(hypertension, 'Papaya - rich in potassium and antioxidants').

recommended_food(heart_disease, 'Mackerel (Scomber) - high omega-3, reduces heart disease risk').
recommended_food(heart_disease, 'Sardine (Salaya) - omega-3 fatty acids, heart protective').
recommended_food(heart_disease, 'Oats - reduces LDL cholesterol, protects arteries').
recommended_food(heart_disease, 'Gotu Kola - improves circulation and heart health').
recommended_food(heart_disease, 'Walnuts - heart-healthy omega-3 and antioxidants').
recommended_food(heart_disease, 'Flaxseeds - omega-3 and fiber, lowers cholesterol').
recommended_food(heart_disease, 'Spinach (Nivithi) - folate and magnesium for heart health').

recommended_food(ckd, 'Cabbage - low potassium, low phosphorus, kidney friendly').
recommended_food(ckd, 'Cauliflower - low potassium, anti-inflammatory').
recommended_food(ckd, 'Apple - low potassium fruit, kidney safe').
recommended_food(ckd, 'Egg Whites - high quality protein with less phosphorus than yolk').
recommended_food(ckd, 'White Rice - lower potassium and phosphorus than brown rice').
recommended_food(ckd, 'Cassava (well-boiled, water discarded) - reduces potassium content').
recommended_food(ckd, 'Cucumber - very low potassium, hydrating and kidney friendly').

recommended_food(cholesterol, 'Oats - beta-glucan fiber reduces LDL cholesterol').
recommended_food(cholesterol, 'Kurakkan (Finger Millet) - high fiber, lowers cholesterol').
recommended_food(cholesterol, 'Flaxseeds - omega-3 and soluble fiber').
recommended_food(cholesterol, 'Mackerel (Scomber) - omega-3 raises HDL good cholesterol').
recommended_food(cholesterol, 'Eggplant (Wambatu) - absorbs cholesterol in digestion').
recommended_food(cholesterol, 'Barley - beta-glucan content reduces LDL').
recommended_food(cholesterol, 'Green Tea - antioxidants reduce LDL oxidation').

% ================================
% NUTRITION INFORMATION
% Matches "View Nutrition Information" use case (Figure 3.1)
% ================================
nutrition_info(diabetes,
    'Calories: 1600-2000 kcal/day | Carbs: 45-60% (low GI only) | Fiber: 25-35g/day | Sugar: <5% total calories | Protein: 15-20% | Fat: 25-30% (unsaturated preferred)').

nutrition_info(hypertension,
    'Calories: 1800-2200 kcal/day | Sodium: <1500mg/day | Potassium: 3500-4700mg/day | Magnesium: 400mg/day | Fiber: 25-30g/day | Saturated Fat: <6%').

nutrition_info(heart_disease,
    'Calories: 1600-2000 kcal/day | Total Fat: <30% | Saturated Fat: <7% | Trans Fat: 0% | Omega-3: 1-2g/day | Fiber: 25-35g/day | Cholesterol: <200mg/day').

nutrition_info(ckd,
    'Calories: 30-35 kcal/kg body weight | Protein: 0.6-0.8g/kg (restricted) | Potassium: <2000mg/day | Phosphorus: <800mg/day | Sodium: <2000mg/day | Fluid: as advised by doctor').

nutrition_info(cholesterol,
    'Calories: 1800-2200 kcal/day | Saturated Fat: <7% | Trans Fat: 0% | Dietary Cholesterol: <200mg/day | Soluble Fiber: 10-25g/day | Omega-3: 1-2g/day').

% ================================
% FOODS TO AVOID (5 per disease)
% ================================
avoid(diabetes, 'White rice in large portions').
avoid(diabetes, 'Sugar and sugary drinks (soft drinks, fruit juices)').
avoid(diabetes, 'White bread and refined flour products').
avoid(diabetes, 'Fried foods and fast food').
avoid(diabetes, 'Sweetened condensed milk and dairy desserts').

avoid(hypertension, 'Table salt and salty foods (pickles, papadam)').
avoid(hypertension, 'Processed and canned foods (high sodium)').
avoid(hypertension, 'Fried and oily foods').
avoid(hypertension, 'Alcohol and caffeinated drinks').
avoid(hypertension, 'Red meat and full-fat dairy products').

avoid(heart_disease, 'Fried foods and trans fats (puff pastry, deep-fried snacks)').
avoid(heart_disease, 'Coconut oil and coconut milk in large amounts').
avoid(heart_disease, 'Red meat and organ meats (liver, kidney)').
avoid(heart_disease, 'Full-fat dairy (butter, ghee, cream)').
avoid(heart_disease, 'Processed meats (sausages, salami)').

avoid(ckd, 'Bananas and potassium-rich fruits (oranges, avocado)').
avoid(ckd, 'High protein foods in excess (red meat, large fish portions)').
avoid(ckd, 'Salty and processed foods (canned goods, pickles)').
avoid(ckd, 'Dark leafy greens in large portions (high potassium)').
avoid(ckd, 'Phosphorus-rich foods (dairy, nuts, cola drinks)').

avoid(cholesterol, 'Ghee and butter').
avoid(cholesterol, 'Fried foods and fast food').
avoid(cholesterol, 'Coconut milk and coconut oil in large amounts').
avoid(cholesterol, 'Full-fat dairy products (whole milk, cream, cheese)').
avoid(cholesterol, 'Red meat and processed meats (sausages, bacon)').

% ================================
% COMMON AVOID (clean merged list for multi-disease)
% ================================
common_avoid([diabetes, hypertension], 'Fried foods and fast food').
common_avoid([diabetes, hypertension], 'Processed and packaged foods').
common_avoid([diabetes, hypertension], 'Sugary and salty snacks (biscuits, chips)').
common_avoid([diabetes, hypertension], 'White bread and refined carbohydrates').
common_avoid([diabetes, hypertension], 'Sweetened drinks and alcohol').

common_avoid([diabetes, heart_disease], 'Fried foods and trans fats').
common_avoid([diabetes, heart_disease], 'Coconut milk and coconut oil in large amounts').
common_avoid([diabetes, heart_disease], 'Sugar and sugary drinks').
common_avoid([diabetes, heart_disease], 'White rice in large portions').
common_avoid([diabetes, heart_disease], 'Full-fat dairy and processed meats').

common_avoid([hypertension, heart_disease], 'Fried and oily foods').
common_avoid([hypertension, heart_disease], 'Salt and high-sodium processed foods').
common_avoid([hypertension, heart_disease], 'Red meat and full-fat dairy').
common_avoid([hypertension, heart_disease], 'Alcohol and caffeinated drinks').
common_avoid([hypertension, heart_disease], 'Coconut oil and butter').

common_avoid([diabetes, cholesterol], 'Fried foods and fast food').
common_avoid([diabetes, cholesterol], 'Sugar and sweetened drinks').
common_avoid([diabetes, cholesterol], 'Full-fat dairy products').
common_avoid([diabetes, cholesterol], 'Coconut milk and coconut oil in excess').
common_avoid([diabetes, cholesterol], 'White bread and refined flour products').

common_avoid([hypertension, cholesterol], 'Fried and oily foods').
common_avoid([hypertension, cholesterol], 'Salt, processed and canned foods').
common_avoid([hypertension, cholesterol], 'Red meat and processed meats').
common_avoid([hypertension, cholesterol], 'Full-fat dairy products').
common_avoid([hypertension, cholesterol], 'Alcohol').

common_avoid([heart_disease, cholesterol], 'Fried foods and trans fats').
common_avoid([heart_disease, cholesterol], 'Coconut oil, butter and ghee').
common_avoid([heart_disease, cholesterol], 'Red meat and organ meats').
common_avoid([heart_disease, cholesterol], 'Full-fat dairy products').
common_avoid([heart_disease, cholesterol], 'Processed meats (sausages, bacon)').

common_avoid([diabetes, hypertension, heart_disease], 'Fried foods and fast food').
common_avoid([diabetes, hypertension, heart_disease], 'Salt, sugar and all processed foods').
common_avoid([diabetes, hypertension, heart_disease], 'Coconut milk and coconut oil').
common_avoid([diabetes, hypertension, heart_disease], 'Full-fat dairy and red meat').
common_avoid([diabetes, hypertension, heart_disease], 'White rice, white bread and refined carbohydrates').

common_avoid([diabetes, ckd], 'Salty and processed foods').
common_avoid([diabetes, ckd], 'Sugar and sweetened drinks').
common_avoid([diabetes, ckd], 'High potassium fruits (banana, orange, avocado)').
common_avoid([diabetes, ckd], 'High protein foods in excess').
common_avoid([diabetes, ckd], 'Phosphorus-rich foods (nuts, dairy, cola)').

common_avoid([hypertension, ckd], 'Salt and all high-sodium foods').
common_avoid([hypertension, ckd], 'Processed and canned foods').
common_avoid([hypertension, ckd], 'High potassium foods (banana, avocado)').
common_avoid([hypertension, ckd], 'Red meat and full-fat dairy').
common_avoid([hypertension, ckd], 'Alcohol and caffeinated drinks').

% ================================
% HEALTH TIPS (per disease)
% ================================
tip(diabetes, 'Prefer red rice or kurakkan over white rice. Include bitter gourd, mukunuwenna and leafy greens daily. Eat small meals every 3-4 hours to control blood sugar.').
tip(hypertension, 'Minimize salt intake. Use garlic, lime and herbs for flavor. Exercise regularly and avoid stress. Increase potassium-rich but blood-pressure friendly foods.').
tip(heart_disease, 'Include oily fish (mackerel, sardine) at least twice a week. Reduce oil and avoid coconut milk. Do 30 minutes of moderate exercise daily.').
tip(ckd, 'Control protein and potassium intake carefully. Follow your doctor\'s specific dietary plan. Stay well hydrated with plain water. Avoid phosphorus-rich foods.').
tip(cholesterol, 'Eat oats daily. Replace coconut oil with small amounts of canola or olive oil. Avoid all fried items. Include fish rich in omega-3 regularly.').

% ================================
% MULTI-DISEASE TIPS
% ================================
multi_tip([diabetes, hypertension], 'Control both sugar and salt. Use cinnamon, garlic and lime for flavor instead. Eat kurakkan and red rice in moderate portions. Avoid all processed foods.').
multi_tip([diabetes, heart_disease], 'Focus on low glycemic, low-fat meals. Include omega-3 rich fish, bitter gourd and leafy greens daily. Avoid fried foods, sugar and coconut milk.').
multi_tip([hypertension, heart_disease], 'Strictly reduce salt and saturated fat. Grilled or steamed preparation only. Include oily fish twice a week. Maintain healthy weight and exercise daily.').
multi_tip([diabetes, cholesterol], 'Avoid sugar and saturated fat together. Oats, kurakkan and bitter gourd are your best friends. Use grilling or steaming instead of frying.').
multi_tip([hypertension, cholesterol], 'Avoid both salt and saturated fat. Choose grilled fish and chicken over red meat. Increase fiber through oats, kurakkan and vegetables.').
multi_tip([heart_disease, cholesterol], 'Low fat and low oil diet is critical. Grill or steam all meals. Include omega-3 fish, oats and leafy greens daily. Avoid all forms of processed fat.').
multi_tip([diabetes, hypertension, heart_disease], 'Triple condition requires strict discipline: no sugar, no salt, low oil. Kurakkan, red rice, oily fish, leafy greens and bitter gourd are your staples. Consult your nutritionist for a detailed monthly plan.').
multi_tip([diabetes, ckd], 'Managing diabetes with CKD is complex. Limit both sugar and potassium. Follow your nephrologist protein restriction plan strictly. Plain white rice and low-potassium vegetables are safest.').

% ================================
% AGE-BASED TIPS
% Satisfies proposal Objective 1: age accessibility
% ================================
age_tip(child, diabetes, 'Avoid bitter-tasting foods. Include sweet potato, mild dhal and soft kurakkan roti. Ensure 3 regular meals and 2 snacks daily. Involve parents in meal planning.').
age_tip(child, hypertension, 'Use no added salt. Offer banana, papaya and cucumber as snacks. Avoid all packaged snacks and fizzy drinks. Ensure adequate potassium through fresh fruits.').
age_tip(child, heart_disease, 'Avoid fried snacks entirely. Offer oats porridge, grilled fish and fresh fruit. Keep portions child-sized. Encourage physical play for 60 minutes daily.').
age_tip(child, ckd, 'Strict portion control is critical. Consult a pediatric nephrologist for exact allowances. Offer apple, pear, rice crackers as safe snacks. Avoid all salty packaged foods.').
age_tip(child, cholesterol, 'Replace fried snacks with oat biscuits and fruit. Include grilled fish twice a week. Avoid butter on bread. Encourage active play to support healthy cholesterol.').

age_tip(young, diabetes, 'Balance meals with study or work schedules. Avoid skipping meals as it causes sugar spikes. Carry almonds or fruit as snacks. Limit sugary energy drinks completely.').
age_tip(young, hypertension, 'Reduce caffeine and energy drinks. Exercise at least 30 minutes daily. Cook with herbs instead of salt. Avoid fast food and takeaways as much as possible.').
age_tip(young, heart_disease, 'Start heart-healthy habits now. Exercise regularly, avoid smoking and reduce stress. Include oily fish twice a week. Replace fried snacks with fruit and nuts.').
age_tip(young, ckd, 'Stay well hydrated as advised by your doctor. Avoid over-the-counter pain medications. Follow your protein restriction carefully. Get regular kidney function tests.').
age_tip(young, cholesterol, 'Establish healthy habits now. Replace fast food with home-cooked meals. Include oats at breakfast daily. Exercise regularly to raise HDL good cholesterol.').

age_tip(adult, diabetes, 'Prefer red rice or kurakkan over white rice. Include bitter gourd, mukunuwenna and leafy greens daily. Eat small meals every 3-4 hours to control blood sugar.').
age_tip(adult, hypertension, 'Minimize salt intake. Use garlic, lime and herbs for flavor. Exercise regularly and avoid stress. Increase potassium through blood-pressure friendly foods.').
age_tip(adult, heart_disease, 'Include oily fish at least twice a week. Reduce oil and avoid coconut milk. Do 30 minutes of moderate exercise daily. Monitor cholesterol levels regularly.').
age_tip(adult, ckd, 'Control protein and potassium intake carefully. Follow your doctor dietary plan. Stay well hydrated with plain water. Avoid phosphorus-rich foods.').
age_tip(adult, cholesterol, 'Eat oats daily. Replace coconut oil with small amounts of canola or olive oil. Avoid all fried items. Include omega-3 rich fish regularly.').

age_tip(elderly, diabetes, 'Prefer soft-cooked red rice or well-cooked kurakkan roti. Eat smaller portions 5 times a day. Monitor blood sugar regularly. Stay hydrated and avoid skipping meals.').
age_tip(elderly, hypertension, 'Use minimal salt and avoid pickles. Eat soft cooked vegetables. Sit down after meals and avoid sudden position changes. Monitor blood pressure daily at home.').
age_tip(elderly, heart_disease, 'Prefer steamed or boiled foods over grilled. Include soft fish like sardine or tuna. Avoid heavy meals at night. Take short gentle walks daily if mobility allows.').
age_tip(elderly, ckd, 'Protein and potassium restrictions are especially important in old age. Eat small, frequent meals. Ensure adequate hydration as advised. Regular kidney function monitoring is critical.').
age_tip(elderly, cholesterol, 'Prefer boiled or steamed preparation. Include oats porridge at breakfast every day. Avoid all fried foods. Short daily walks help manage cholesterol alongside diet.').

% ================================
% SAFE MEAL SELECTION (multi-disease aware)
% ================================
meal_covers_diseases(MealDiseases, RequiredDiseases) :-
    forall(member(D, RequiredDiseases), member(D, MealDiseases)).

safe_breakfast(Diseases, Pref, Allergies, Meal) :-
    findall(M,
        (breakfast_meal(M, Pref, Tags, MealDiseases),
         meal_covers_diseases(MealDiseases, Diseases),
         safe_from_allergies(Tags, Allergies)),
        List),
    (List \= [] ->
        random_select(List, Meal)
    ;
        Meal = 'Please consult a nutritionist for a safe breakfast matching all your conditions').

safe_lunch_dinner(Diseases, Pref, Allergies, Meal) :-
    findall(M,
        (lunch_dinner_meal(M, Pref, Tags, MealDiseases),
         meal_covers_diseases(MealDiseases, Diseases),
         safe_from_allergies(Tags, Allergies)),
        List),
    (List \= [] ->
        random_select(List, Meal)
    ;
        Meal = 'Please consult a nutritionist for a safe meal matching all your conditions').

safe_snack(Diseases, Allergies, Snack) :-
    findall(S,
        (evening_snack(S, Tags, SnackDiseases),
         meal_covers_diseases(SnackDiseases, Diseases),
         safe_from_allergies(Tags, Allergies)),
        List),
    (List \= [] ->
        random_select(List, Snack)
    ;
        Snack = 'Fresh fruit safe for your conditions (consult nutritionist)').

safe_beverage(Diseases, Allergies, Beverage) :-
    findall(B,
        (herbal_beverage(B, Tags, BevDiseases),
         meal_covers_diseases(BevDiseases, Diseases),
         safe_from_allergies(Tags, Allergies)),
        List),
    (List \= [] ->
        random_select(List, Beverage)
    ;
        Beverage = 'Plain warm water with lemon (safest universal option)').

safe_from_allergies(Tags, Allergies) :-
    forall(member(T, Tags), \+ member(T, Allergies)).

% ================================
% AVOID LIST (no duplicates, multi-disease aware)
% ================================
get_avoid_list(Diseases, Unique) :-
    length(Diseases, Len),
    (Len > 1 ->
        msort(Diseases, Sorted),
        (findall(F, common_avoid(Sorted, F), List), List \= [] ->
            list_to_set(List, Unique)
        ;
            findall(F, (member(D, Diseases), avoid(D, F)), All),
            list_to_set(All, Unique)
        )
    ;
        findall(F, (member(D, Diseases), avoid(D, F)), All),
        list_to_set(All, Unique)
    ).

% ================================
% RECOMMENDED FOODS (merged, no duplicates)
% ================================
get_recommended_foods(Diseases, Foods) :-
    findall(F, (member(D, Diseases), recommended_food(D, F)), All),
    list_to_set(All, Foods).

% ================================
% NUTRITION INFO
% ================================
get_nutrition_info(Diseases, InfoList) :-
    findall(D-I, (member(D, Diseases), nutrition_info(D, I)), InfoList).

% ================================
% TIPS (age-aware, multi-disease aware)
% ================================
get_tip(Diseases, AgeGroup, Tip) :-
    msort(Diseases, Sorted),
    (multi_tip(Sorted, MultiTip) ->
        Tip = MultiTip
    ;
        get_main_disease(Diseases, Main),
        (age_tip(AgeGroup, Main, AgeTip) ->
            Tip = AgeTip
        ;
            tip(Main, Tip)
        )
    ).

get_main_disease([D|_], D).

% ================================
% RANDOM SELECTION (fixed off-by-one)
% ================================
random_select(List, Item) :-
    length(List, Len),
    Len > 0,
    Max is Len - 1,
    random_between(0, Max, I),
    nth0(I, List, Item).

% ================================
% MAIN RECOMMEND PREDICATE
% ================================
recommend(Diseases, AgeGroup, Preference, Allergies) :-
    is_list(Diseases), Diseases \= [],
    forall(member(D, Diseases), disease(D)),
    age_group(AgeGroup),
    preference(Preference),
    (Allergies = [] ; forall(member(A, Allergies), allergy(A))),

    safe_breakfast(Diseases, Preference, Allergies, Breakfast),
    safe_lunch_dinner(Diseases, Preference, Allergies, Lunch),
    safe_lunch_dinner(Diseases, Preference, Allergies, Dinner),
    safe_snack(Diseases, Allergies, Snack),
    safe_beverage(Diseases, Allergies, Beverage),

    get_avoid_list(Diseases, Avoid),
    get_recommended_foods(Diseases, RecommendedFoods),
    get_nutrition_info(Diseases, NutritionList),
    get_tip(Diseases, AgeGroup, Tip),

    nl,
    write('============================================================'), nl,
    write('       PERSONALIZED SRI LANKAN MEAL PLAN                   '), nl,
    write('============================================================'), nl,
    write('Diseases      : '), write(Diseases), nl,
    write('Age Group     : '), write(AgeGroup), nl,
    write('Preference    : '), write(Preference), nl,
    write('Allergies     : '), write(Allergies), nl,
    write('------------------------------------------------------------'), nl,
    write('BREAKFAST     : '), write(Breakfast), nl,
    write('LUNCH         : '), write(Lunch), nl,
    write('EVENING SNACK : '), write(Snack), nl,
    write('DINNER        : '), write(Dinner), nl,
    write('BEVERAGE      : '), write(Beverage), nl,
    write('------------------------------------------------------------'), nl,
    nl, write('=== HIGHLY RECOMMENDED FOODS ==='), nl,
    forall(member(R, RecommendedFoods), (write('  + '), write(R), nl)),
    nl, write('=== FOODS TO AVOID ==='), nl,
    forall(member(F, Avoid), (write('  - '), write(F), nl)),
    nl, write('=== NUTRITION TARGETS ==='), nl,
    forall(member(D-Info, NutritionList),
        (write('  ['), write(D), write(']: '), write(Info), nl)),
    nl, write('=== HEALTH TIP ==='), nl,
    write(Tip), nl.

% ================================
% ERROR HANDLING
% ================================
recommend(Diseases, _, _, _) :-
    (   \+ is_list(Diseases)
    ;   Diseases = []
    ;   \+ forall(member(D, Diseases), disease(D))
    ), !,
    write('Invalid input! Please check your diseases list.'), nl,
    write('Valid diseases: diabetes, hypertension, heart_disease, ckd, cholesterol'), nl,
    write('Example: recommend([diabetes, hypertension], adult, veg, [milk, egg]).'), nl.

recommend(_, AgeGroup, _, _) :-
    \+ age_group(AgeGroup), !,
    write('Invalid age group! Valid values: child, young, adult, elderly'), nl.

recommend(_, _, Preference, _) :-
    \+ preference(Preference), !,
    write('Invalid preference! Valid values: veg, nonveg'), nl.

% ================================
% QUERY EXAMPLES
% ================================
% Single disease:
%   recommend([diabetes], adult, veg, []).
%   recommend([hypertension], elderly, nonveg, [fish]).
%   recommend([ckd], adult, veg, [egg, milk]).
%   recommend([cholesterol], young, veg, []).
%   recommend([heart_disease], elderly, nonveg, []).
%
% Multiple diseases:
%   recommend([diabetes, hypertension], adult, veg, []).
%   recommend([diabetes, heart_disease], elderly, nonveg, [fish, tree_nut]).
%   recommend([hypertension, heart_disease], adult, nonveg, []).
%   recommend([diabetes, hypertension, heart_disease], adult, veg, [wheat]).
%   recommend([diabetes, ckd], adult, nonveg, [egg]).
%
% Age-specific tips (tip changes per age group):
%   recommend([diabetes], child, veg, []).
%   recommend([hypertension], elderly, veg, []).
%   recommend([cholesterol], young, nonveg, [fish]).
