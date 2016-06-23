<?php
ini_set('display_errors', true);
include("../../app/Mage.php");

$storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
include("../admin/config/db.php");
    // post data
    $email = $_POST['email'];
	$insid = $_POST['insid'];
	$musthave_product = $_POST['musthave_productdb'];
	$recomended_product = $_POST['recommended_productdb'];
	$useremail = $email;
	
	$result = $conn->query("SELECT * FROM skintools_emails WHERE address = '".$useremail."'");
	$row_cnt = $result->num_rows;
	
	if($insid!='' && $row_cnt > 0) {
		
		$rows = mysqli_fetch_array($result);
		$rowId = $rows['id'];
		$stmt = $conn->query("UPDATE skintools_emails SET questiondata_id = '".$insid."' WHERE id = '".$rowId."'");
		$stmt = $conn->query("UPDATE skintools_questionsdata SET musthave_product = '".$musthave_product."', recommended_product = '".$recomended_product."' WHERE id = '".$insid."'");

	} else {
		
		$stmt = $conn->query("INSERT INTO skintools_emails (address, questiondata_id) VALUES ('".$useremail."', '".$insid."')");
		$stmt = $conn->query("UPDATE skintools_questionsdata SET musthave_product = '".$musthave_product."', recommended_product = '".$recomended_product."' WHERE id = '".$insid."'");
	
	}
    $conn->close();

    echo("hello");
	
	

    $m = $_GET['m'];
    $r = $_GET['r'];
    $price1 = $_GET['price1'];
    $size1 = $_GET['size1'];
    $price2 = $_GET['price2'];
    $size2 = $_GET['size2'];

    //1
    $MiracleCleanser_t = "Miracle Cleanser";
    $MiracleCleanser_p = "This intelligent, aromatic, creamy infusion glides on to the skin and lifts off all impurities and make-up. Fusing our signature technologies with BioOrganic ingredients, this cleanser, along with its antibacterial bamboo muslin cloth, soothes, hydrates and brightens all skin types.";
    $img1 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_cleanser.jpg";
    $img1_link = $storeUrl."index.php/products/aurelia-miracle-cleanser";

    //2
    $RefineAndPolishMiracle_t = "Refine & Polish Miracle Balm";
    $RefineAndPolishMiracle_p = "This revolutionary double-action enzyme polish works to transform and brighten dull, rough, congested and lacklustre complexions. Containing natural rice bran refining beads to gently dislodge dry skin cells, boosting circulation through manual massage to reveal bright, clear and beautiful skin.";
    $img2 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_exfoliator.jpg";
    $img2_link = $storeUrl."index.php/products/refine-and-polish-miracle-balm";

    //3
    $RevitaliseAndGlowSerum_t = "Revitalise & Glow Serum";
    $RevitaliseAndGlowSerum_p = "A lightweight, easily absorbed potent fluid fusing our signature technologies with BioOrganic plant and flower essences. Depositing probiotics, peptides and antioxidants where they are most needed, boosting skin’s radiance and glow.";
    $img3 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_serum.jpg";
    $img3_link = $storeUrl."index.php/products/revitalise-and-glow-serum";

    //4
    $CellRevitaliseDayMoisturiser_t = "Cell Revitalise Day Moisturiser";
    $CellRevitaliseDayMoisturiser_p = "This lightly whipped, clinically advanced cream fuses probiotic and peptide technologies with BioOrganic ingredients to hydrate and soothe dull and dehydrated skin. It creates the perfect smooth base for make-up while helping to boost radiance and glow.";
    $img4 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_day.jpg";
    $img4_link = $storeUrl."index.php/products/cell-revitalise-day-moisturiser";

    //5
    $CellRevitaliseNightMoisturiser_t = "Cell Revitalise Night Moisturiser";
    $CellRevitaliseNightMoisturiser_p = "A sumptuous fragrance blend of Neroli, Lavender, Rose and Mandarin adds to the luxury of this nourishing, clinically advanced cream. Signature technologies and BioOrganic ingredients hydrate and soothe dull and dehydrated skin, promoting a radiant glow while helping to prevent skin ageing.";
    $img5 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_night.jpg";
    $img5_link = $storeUrl."index.php/products/cell-revitalise-night-moisturiser";

    //6
    $CellRepairNightOil_t = "Cell Repair Night Oil";
    $CellRepairNightOil_p = "An intensive overnight boosting treatment which works in tandem with the skin's nightly repair mode. BioOrganic 100% pure botanicals ensure this exquisite oil instantly absorbs to work on all fronts.";
    $img6 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_nightoil.jpg";
    $img6_link = $storeUrl."index.php/products/cell-repair-night-oil";

    //7
    $EyeRevitalisingDuo_t = "Eye Revitalising Duo";
    $EyeRevitalisingDuo_p = "Our eye complex has been scientifically proven to result in an age correction improvement of 5 years in just 28 days of daily use. Benefitting from our signature technologies, these two luxurious products work in synergy to revolutionise the eye area.";
    $img7 = "#";

    //8
    $CellRevitaliseRoseMask_t = "Cell Revitalise Rose Mask";
    $CellRevitaliseRoseMask_p = "This indulgent, soothing ‘red carpet’ Rose Mask delivers botanicals and probiotics to the skin, firming, hydrating and restoring balance. Damask Rose Absolute produces an exquisitely scented floral note and adds a warm serenity to the blend.";
    $img8 = "http://aureliaskincare.com/skintools/email/images/productimages_200x250_rose.jpg";
    $img8_link = $storeUrl."index.php/products/cell-revitalise-rose-mask";

    //9
    $FirmAndRevitaliseDryBodyOil_t = "Firm & Revitalise Dry Body Oil";
    $FirmAndRevitaliseDryBodyOil_p = "A nourishing multi-use dry body oil that firms and deeply hydrates parched and lackluster skin. Our sumptuous essential oil blend of Neroli, Lavender, Rose and Mandarin, combined with antioxidant-rich BioOrganic botanicals works to revitalise the skin, soothe the senses and combat stress.";
    $img9 = "#";

    $top_logo = "http://aureliaskincare.com/skintools/email/images/email/top.png";
    $bg_image = $storeUrl."skin/frontend/default/star-aurelia-responsive/images/bg-tile.jpg";
    $buy_logo = "http://aureliaskincare.com/skintools/email/images/10off.png";
    $musthave_t = "";
    $musthave_p = "";
    $musthave_img = "";
    $recommended_t = "";
    $recommended_p = "";
    $recommended_img = "";
    $recommended_img_m_link = "";
    $recommended_img_r_link = "";




    if($m == 1 || $m == "1"){

        $musthave_t = $MiracleCleanser_t;
        $musthave_p = $MiracleCleanser_p;
        $musthave_img = $img1;
        $recommended_img_m_link = $img1_link;

    }

    if($r == 1 || $r == "1"){

        $recommended_t = $MiracleCleanser_t;
        $recommended_p = $MiracleCleanser_p;
        $recommended_img = $img1;
        $recommended_img_r_link = $img1_link;

    }

    if($m == 2 || $m == "2"){

        $musthave_t = $RefineAndPolishMiracle_t;
        $musthave_p = $RefineAndPolishMiracle_p;
        $musthave_img = $img2;
        $recommended_img_m_link = $img2_link;
    }

    if($r == 2 || $r == "2"){

        $recommended_t = $RefineAndPolishMiracle_t;
        $recommended_p = $RefineAndPolishMiracle_p;
        $recommended_img = $img2;
        $recommended_img_r_link = $img2_link;

    }

    if($m == 3 || $m == "3"){

        $musthave_t = $RevitaliseAndGlowSerum_t;
        $musthave_p = $RevitaliseAndGlowSerum_p;
        $musthave_img = $img3;
        $recommended_img_m_link = $img3_link;
    }

    if($r == 3 || $r == "3"){

        $recommended_t = $RevitaliseAndGlowSerum_t;
        $recommended_p = $RevitaliseAndGlowSerum_p;
        $recommended_img = $img3;
        $recommended_img_r_link = $img3_link;
    }

    if($m == 4 || $m == "4"){

        $musthave_t = $CellRevitaliseDayMoisturiser_t;
        $musthave_p = $CellRevitaliseDayMoisturiser_p;
        $musthave_img = $img4;
        $recommended_img_m_link = $img4_link;
    }

    if($r == 4 || $r == "4"){

        $recommended_t = $CellRevitaliseDayMoisturiser_t;
        $recommended_p = $CellRevitaliseDayMoisturiser_p;
        $recommended_img = $img4;
        $recommended_img_r_link = $img4_link;

    }

    if($m == 5 || $m == "5"){

        $musthave_t = $CellRevitaliseNightMoisturiser_t;
        $musthave_p = $CellRevitaliseNightMoisturiser_p;
        $musthave_img = $img5;
        $recommended_img_m_link = $img5_link;
    }

    if($r == 5 || $r == "5"){

        $recommended_t = $CellRevitaliseNightMoisturiser_t;
        $recommended_p = $CellRevitaliseNightMoisturiser_p;
        $recommended_img = $img5;
        $recommended_img_r_link = $img5_link;

    }

    if($m == 6 || $m == "6"){

        $musthave_t = $CellRepairNightOil_t;
        $musthave_p = $CellRepairNightOil_p;
        $musthave_img = $img6;
        $recommended_img_m_link = $img6_link;

    }

    if($r == 6 || $r == "6"){

        $recommended_t = $CellRepairNightOil_t;
        $recommended_p = $CellRepairNightOil_p;
        $recommended_img = $img6;
        $recommended_img_r_link = $img6_link;
    }

    // if($m == 7 || $m == "7"){

    //     $musthave_t = $EyeRevitalisingDuo_t;
    //     $musthave_p = $EyeRevitalisingDuo_p;
    // }

    // if($r == 7 || $r == "7"){

    //     $recommended_t = $EyeRevitalisingDuo_t;
    //     $recommended_p = $EyeRevitalisingDuo_p;
    // }

    if($m == 8 || $m == "8"){

        $musthave_t = $CellRevitaliseRoseMask_t;
        $musthave_p = $CellRevitaliseRoseMask_p;
        $musthave_img = $img8;
        $recommended_img_m_link = $img8_link;

    }

    if($r == 8 || $r == "8"){

        $recommended_t = $CellRevitaliseRoseMask_t;
        $recommended_p = $CellRevitaliseRoseMask_p;
        $recommended_img = $img8;
        $recommended_img_r_link = $img8_link;

    }

    // if($m == 9 || $m == "9"){

    //     $musthave_t = $FirmAndRevitaliseDryBodyOil_t;
    //     $musthave_p = $FirmAndRevitaliseDryBodyOil_p;
    // }

    // if($r == 9 || $r == "9"){

    //     $recommended_t = $FirmAndRevitaliseDryBodyOil_t;
    //     $recommended_p = $FirmAndRevitaliseDryBodyOil_p;
    // }

    //questions

    echo "<h1>How does your day begin</h1>";     
    $post_q2 = split( ",", $_POST['post_q2'] );
    $post_q2_len = sizeof($post_q2) - 1;

    $begin_html_tag = "";
    $break_loop = 0;

    $hide_title = 0;
   
    for ($i = 0; $i <= $post_q2_len - 1; $i++){


        if( ! in_array('breakfast', $post_q2, true) && $hide_title != 1 ){

           $begin_html_tag .= "<i style=\" font-style: normal;color:#706f6f;\">Skipping breakfast can trigger cravings for quick, unhealthy food fixes.</i>" . 
           "<p style=\"color:#706f6f;\">Having breakfast will boost your metabolism and increase your energy throughout the day. What you're eating can really help your skin too, and breakfast is such a good place to start; try sprinkling over omega-rich nuts and seeds to help keep skin healthy and supple. Green superfood smoothies are great for a vitamin hit and make skin glow.</p>";
            $break_loop = 1;
            $hide_title = 1;
        }
       
        if ($post_q2[$i] == "excercise") {
           $begin_html_tag .= "<i style=\" font-style: normal;color:#706f6f;\">Exercising first thing is a great way to start the day as it boosts your circulation giving you a natural glow.</i>" . 
           "<p style=\"color:#706f6f;\">It also increases your body's metabolism and raises energy levels to get the endorphins going which help make us happy so keep it up!</p>";
       
        } else

        if ($post_q2[$i] == "shower") {
            
           $begin_html_tag .= "<i style=\" font-style: normal;color:#706f6f;\">An energising morning shower can make you feel awake and refreshed but don't forget to think about how the products you use can affect your skin.</i>" . 
           "<p style=\"color:#706f6f;\">Try to avoid shower gels and shampoos containing sulfates which can severely dry your skin and scalp. Before showering don't skip your morning cleanse! It's important to keep your face and body care separate. You might like to try applying the Refine & Polish Miracle Balm three times a week to clean skin and leaving on while you rush around before hopping in the shower to remove!</p>";

        } else

        if ($post_q2[$i] == "social_media") {
           $begin_html_tag .= "<i style=\" font-style: normal;color:#706f6f;\">A massive 80% of us check our smart phones first thing and that does not include switching off the alarm!</i>" . 
           "<p style=\"color:#706f6f;\">The most common sentiment regarding a smartphone is one of ‘connectedness‘ and this surpasses ‘overwhelmed' ‘stressed out' and ‘anxious' for many but the line is a thin one and you may need to make a change. Remember stress can affect your skin! Try to have detox mornings when you wake naturally and resist checking your smartphone.</p>";

        } else

        if ($post_q2[$i] == "coffee") {
            
           $begin_html_tag .= "<i style=\" font-style: normal;color:#706f6f;\">A caffeine hit on the go gives you that quick pick up but can be dehydrating for your skin.</i>" . 
           "<p style=\"color:#706f6f;\">Keep a watch on how much coffee you drink throughout the day. Dehydration can cause fine lines and a lacklustre complexion so make like the Italians and have a glass of water with your espresso!</p>";
        }
    }

    $post_q2_height = 0;

    if ($hide_title == 0 && $post_q2_len == 1){

        $post_q2_title = "";

        $post_q2_height = 200;

    }else{
        $post_q2_title = "HOW DOES YOUR DAY BEGIN?";
    }



    $post_q3_t = $_POST['post_q3_t'];
    $post_q3_p = $_POST['post_q3_p'];

    echo "<h1>How do you feel</h1>";             
    echo $post_q3_t . "<br>";
    echo $post_q3_p . "<br>";

    $post_q4_t = $_POST['post_q4_t'];
    $post_q4_p = $_POST['post_q4_p'];

    echo "<h1>What's your morning skincare routine?</h1>";             
    echo $post_q4_t . "<br>";
    echo $post_q4_p . "<br>";

    echo "<h1>The things you don't love about your skin</h1>";    

    $post_q6 = split( ",", $_POST['post_q6'] );
    $post_q6_len = sizeof($post_q6) - 1;
    $post_q6_reults = "";

    for ($i = 0; $i < $post_q6_len; $i++){
            
            //
            //
            //  CHANGE THESE TO NUMBERS 1-9 ON MONDAY
            //
            //
            if ($post_q6[$i] == "1" || $post_q6[$i] == 1) {
                // $(".q6-answer1-content1").show();
                $post_q6_reults.= "<p><i style=\" font-style: normal;color:#706f6f;\">Fine lines</i></p>" .
                "<p style=\"color:#706f6f;\">Fine lines can be the first signs of skin ageing and usually appear around the eyes, brow or mouth. They can be intensified by dehydration in the skin so targeted skincare is your friend here! Use products that treat the skin, enhancing hyaluronic acid production and rich in antioxidants. The Revitalise &amp; Glow Serum is ideal as a concentrated base layer morning and evening, and can be followed with the Cell Repair Night Oil for deeper hydration.</p>";
            } 

            if ($post_q6[$i] == "2" || $post_q6[$i] == 2) {
                // $(".q6-answer1-content2").show();
                $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Deeper wrinkles</i></p>" .
                "<p style=\"color:#706f6f;\">As we age, our collagen strands weaken and cause a wrinkle to form. To help smooth out deeper wrinkles opt for products that will boost your skin's natural collagen production and hyaluronic acid (responsible for the skin's hydration). The Revitalise &amp; Glow Serum is perfect as it contains high levels of our peptide complex which boosts collagen by 140%. Don't forget to focus also on the overall health of your skin, a few wrinkles are normal and characterful and as long as your skin is glowing, you'll exude youth! Maintain your glow with the Cell Revitalise Night Moisturiser; packed with essential fatty acids, soothing probiotics and nourishing shea butter.</p>";
            }

            if ($post_q6[$i] == "3" || $post_q6[$i] == 3) {
                // $(".q6-answer1-content3").show();
                $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Dry or flaky patches</i></p>" .
                "<p style=\"color:#706f6f;\">Cleanse - Dry or flaky skin is crying out for deeply nourishing skincare. You want to be adding as much moisture as possible, and definitely avoiding foaming agents like sulfates which strip the skin of its natural oils and are often the root cause of dryness. Avoid any cleansers containing Sodium Laurel Sulfate or Sodium Laureth Sulfate and opt instead for a creamy cleanser like our Miracle Cleanser.</p>" .
                "<p style=\"color:#706f6f;\">Treat - Banish flaky patches overnight with our nourishing Cell Repair Night Oil which is rich in essential fatty acids. At Aurelia we have shea-based Cell Revitalise Day &amp; Night Moisturisers which will melt in to the skin rather than sit on the surface and will keep skin hydrated all day.</p>";
            }

            if ($post_q6[$i] == "4" || $post_q6[$i] == 4) {
                // $(".q6-answer1-content4").show();
                 $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Large pores</i></p>" .
                "<p style=\"color:#706f6f;\">Don't worry, there are steps you can take to reduce the visibility of large pores and ensure your skin looks smoother. But be aware that they will never completely disappear as they are part of the natural structure of your skin. Make sure you are exfoliating regularly to buff away dead skin with the Refine &amp; Polish Miracle Balm and follow with firming, balancing skincare products like the Revitalise &amp; Glow Serum to strengthen your skin and pore walls. Avoid mineral oil (paraffinum liquidum) which can clog pores causing blackheads or breakouts.</p>";
            }

            if ($post_q6[$i] == "5" || $post_q6[$i] == 5) {
                // $(".q6-answer1-content5").show();
                 $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Shine</i></p>" .
                "<p style=\"color:#706f6f;\">Caused by oily skin, shine is normally something that increases throughout the day. Your skincare should be all about rebalancing natural oils but not by stripping the skin. Continuously washing your face and removing all oils will cause an imbalance in your sebum production, and can actually increase shine and oiliness. Switch your cleanser to a gentle, rebalancing cream like the Miracle Cleanser and hydrate using the lightweight Revitalise &amp; Glow Serum. You might also want to try using blotting sheets which help mattify your skin without adding powder or makeup!</p>";
            }

            if ($post_q6[$i] == "6" || $post_q6[$i] == 6) {
                // $(".q6-answer1-content6").show();
                $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Mild to moderate break outs of spots</i></p>" .
                "<p style=\"color:#706f6f;\">Don't panic! There are ways of treating your skin to reduce breakouts. The opposite of what you might believe, products containing natural oils are actually beneficial. Don't be tied down to ‘oil-free' as your body needs essential fatty acids to heal itself and reduce scarring. Our lightweight and silicone-free Revitalise &amp; Glow Serum or Cell Revitalise Day Moisturiser are perfect for hydrating every day without blocking pores. Try exfoliating regularly with our Refine &amp; Polish Miracle Balm to avoid dead skin build-up which can clog pores and avoid alcohol-based toners or astringents which dry your skin.</p>";
            }

            if ($post_q6[$i] == "7" || $post_q6[$i] == 7) {
                // $(".q6-answer1-content7").show();
                 $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Sun damage/brown spots</i></p>" .
                "<p style=\"color:#706f6f;\">Your melanin production has become patchy - causing sporadic brown spots. This can happen as we age, during pregnancy or due to hormone imbalances. Make sure to protect your skin from harsh sunlight during peak hours using broad spectrum SPF and a hat! Help even your skin tone with products rich in vitamin E and botanicals containing high levels of essential fatty acids like the Cell Repair Night Oil and Cell Revitalise Day &amp; Night Moisturisers.</p>";
            }

            if ($post_q6[$i] == "8" || $post_q6[$i] == 8) {
                // $(".q6-answer1-content8").show();
                 $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Sensitivity</i></p>" .
                "<p style=\"color:#706f6f;\">Try to pinpoint what you're sensitive to so you know what to avoid in your skincare. Here is our list of top ingredients to avoid to help you on your way!</p>
                <ul>
                <p style=\"color:#706f6f;\">&#149; MI or MIT - listed as Methylisothiazolinone - is a harsh preservative known to cause skin irritation.</p>
                <p style=\"color:#706f6f;\">&#149; Sulfates or SLS - visible as sodium laurel sulfate or sodium laureth sulfate - are synthetic foaming agents found in thousands of products which strip the skin of its natural oils and upset the acid mantel, causing sensitivity and dryness.</p>
                <p style=\"color:#706f6f;\">&#149; Synthetic Fragrance - sometimes listed as parfum - can be made up of hundreds of ingredients which aren't all visible and could cause sensitivity. Look instead for natural fragrances like essential oils or flower waters </p>
                </ul> 
                <p style=\"color:#706f6f;\">Patch-testing is great if you are unsure whether your skin will react. Your skin's natural cycle takes 28 days to complete so give IT time to see improvements. We recommend trying our Miracle Cleanser first; so often it is your cleanser which is upsetting the skin most so try switching to our gentle cream. All Aurelia Probiotic Skincare products are dermatologically tested and suitable for all skin types including sensitive.</p>";
            }

            if ($post_q6[$i] == "9" || $post_q6[$i] == 9) {
                // $(".q6-answer1-content9").show();
                $post_q6_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">Redness and broken capillaries</i></p>" .
                "<p style=\"color:#706f6f;\">Red or inflamed skin with broken capillaries can be sensitive to the elements and caused by a number of different factors including genes. Avoid the following ingredients to keep your skin balanced:</p>
                <ul>
                <p style=\"color:#706f6f;\">&#149; MI or MIT - listed as Methylisothiazolinone - is a harsh preservative known to cause skin irritation.</p>
                <p style=\"color:#706f6f;\">&#149; Sulfates or SLS - visible as sodium laurel sulfate or sodium laureth sulfate - are synthetic foaming agents found in thousands of products which strip the skin of its natural oils and upset Synthetic acid mantel, causing sensitivity and dryness.</p>
                <p style=\"color:#706f6f;\">&#149; Synthetic Fragrance - sometimes listed as parfum - can be made up of hundreds of ingredients which aren't all visible and could cause sensitivity. Look instead for natural fragrances like essential oils or flower waters which will be listed in full.</p>
                </ul>
                <p style=\"color:#706f6f;\">Look for soothing, gentle products containing lots of botanicals like shea butter and aloe vera which naturally calm the skin and try less frequent exfoliation, avoiding harsh retinols, astringents or alcohol. For immediate cooling and calming try the Cell Revitalise Rose Mask which helps reduce redness in just 10 minutes. You might find redness gets worse in winter so ensure you are protecting skin before you brave the elements with plenty of Cell Revitalise Day or Night Moisturiser or Cell Repair Night Oil. All Aurelia Probiotic Skincare products are dermatologically tested and suitable for all skin types including sensitive. </p>";
            }

            if ($post_q6[$i] == "10" || $post_q6[$i] == 10){

                $post_q6_reults = "<p style=\"color:#706f6f;\">Hooray! It's wonderful to hear that you don't have any major concerns when it comes to your skin. Try the Miracle Cleanser and Cell Revitalise Day Moisturiser for a simple, effective routine.</p>";
            }
    }

    echo $post_q6_reults ;

    $post_q7_t = $_POST['post_q7_t'];
    $post_q7_p = $_POST['post_q7_p'];         
    echo "<h1>How you'd love to look</h1>";   
    echo $post_q7_t . "<br>";
    echo $post_q7_p . "<br>";



    $post_q8 = $_POST['post_q8'];
    echo "<h1>WE DON'T OFTEN GET ASKED HOW OLD WE THINK WE LOOK</h1>";   
    $post_q8_result = "";

    if ($post_q8 == "4" || $post_q8 == 4){
        
        $post_q8_result = "
        <p><i style=\" font-style: normal;color:#706f6f;\">Studies show that 50% of women worry about their skin and how they look which is closely linked to ageing and skin health. We wanted to share what's actually happening to your skin as it ages. Don't forget that with great skincare you'll be taking steps to stem the worry and enjoy glowing, healthy skin making every day a good day.</i></p>
        <p><i style=\" font-style: normal;color:#706f6f;\">Teens</i></p>
        <p style=\"color:#706f6f\">During teenage years, cells are being produced at an optimum rate and renewing themselves every 28 days so your skin will be very toned and resistant. Hormones can be a challenge causing sebum production to vary which can lead to problematic and acne-prone skin.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Twenties</i>
        <p style=\"color:#706f6f\">Skin in your twenties may still be going through lots of changes. With slightly higher oil production you may still experience breakouts although your overall complexion will be smooth and naturally radiant. Skin tends to settle down throughout your late twenties and establishing a gentle but effective routine here will help skin look fantastic in later life.</p>
        ";
    }   

    if ($post_q8 == "0" || $post_q8 == 0){
         
        $post_q8_result = "
        <p><i style=\" font-style: normal;color:#706f6f;\">Studies show that 50% of women worry about their skin and how they look which is closely linked to ageing and skin health. We wanted to share what's actually happening to your skin as it ages. Don't forget that with great skincare you'll be taking steps to stem the worry and enjoy glowing, healthy skin making every day a good day.</i></p>
        <p><i style=\" font-style: normal;color:#706f6f;\">Teens</i></p>
        <p style=\"color:#706f6f\">During teenage years, cells are being produced at an optimum rate and renewing themselves every 28 days so your skin will be very toned and resistant. Hormones can be a challenge causing sebum production to vary which can lead to problematic and acne-prone skin.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Twenties</i>
        <p style=\"color:#706f6f\">Skin in your twenties may still be going through lots of changes. With slightly higher oil production you may still experience breakouts although your overall complexion will be smooth and naturally radiant. Skin tends to settle down throughout your late twenties and establishing a gentle but effective routine here will help skin look fantastic in later life.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Thirties</i>
        <p style=\"color:#706f6f\">Skin in your thirties is when free radicals are on the loose, expression lines start to make themselves visible and sun worshippers may start to see some light sun-spots so make sure you use a minimum of SPF 30 on your face. Some women suffer adult acne at this age, just when they think things are improving. You might notice skin is not as radiant, as cell turnover decreases and collagen production slows down, causing less plumpness and the beginning of fine lines and early wrinkles. It's not all bad though! Studies show we are at our most beautiful throughout our thirties so make the most of it.</p>
        ";
    }

    if ($post_q8 == "1" || $post_q8 == 1){

        $post_q8_result = "
        <p><i style=\" font-style: normal;color:#706f6f;\">Studies show that 50% of women worry about their skin and how they look which is closely linked to ageing and skin health. We wanted to share what's actually happening to your skin as it ages. Don't forget that with great skincare you'll be taking steps to stem the worry and enjoy glowing, healthy skin making every day a good day.</i></p>
        <p><i style=\" font-style: normal;color:#706f6f;\">Teens</i></p>
        <p style=\"color:#706f6f\">During teenage years, cells are being produced at an optimum rate and renewing themselves every 28 days so your skin will be very toned and resistant. Hormones can be a challenge causing sebum production to vary which can lead to problematic and acne-prone skin.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Twenties</i>
        <p style=\"color:#706f6f\">Skin in your twenties may still be going through lots of changes. With slightly higher oil production you may still experience breakouts although your overall complexion will be smooth and naturally radiant. Skin tends to settle down throughout your late twenties and establishing a gentle but effective routine here will help skin look fantastic in later life.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Thirties</i>
        <p style=\"color:#706f6f\">Skin in your thirties is when free radicals are on the loose, expression lines start to make themselves visible and sun worshippers may start to see some light sun-spots so make sure you use a minimum of SPF 30 on your face. Some women suffer adult acne at this age, just when they think things are improving. You might notice skin is not as radiant, as cell turnover decreases and collagen production slows down, causing less plumpness and the beginning of fine lines and early wrinkles. It's not all bad though! Studies show we are at our most beautiful throughout our thirties so make the most of it.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Forties</i>
        <p style=\"color:#706f6f\">Skin has all the potential to look lovely in your forties. You may still have the to-do list from your thirties but perhaps a bit more time to spend on yourself. At this stage you're looking to keep a youthful glow about your skin, staying hydrated and increasing collagen production. You may have deeper expression lines, a lack of radiance and drier or more dehydrated skin as your natural sebum production decreases.</p>
        ";
    }

    if ($post_q8 == "2" || $post_q8 == 2){

        $post_q8_result = "
        <p><i style=\" font-style: normal;color:#706f6f;\">Studies show that 50% of women worry about their skin and how they look which is closely linked to ageing and skin health. We wanted to share what's actually happening to your skin as it ages. Don't forget that with great skincare you'll be taking steps to stem the worry and enjoy glowing, healthy skin making every day a good day.</i></p>
        <i style=\" font-style: normal;color:#706f6f;\">Thirties</i>
        <p style=\"color:#706f6f;\">Skin in your thirties is when free radicals are on the loose, expression lines start to make themselves visible and sun worshippers may start to see some light sun-spots so make sure you use a minimum of SPF 30 on your face. Some women suffer adult acne at this age, just when they think things are improving. You might notice skin is not as radiant, as cell turnover decreases and collagen production slows down, causing less plumpness and the beginning of fine lines and early wrinkles. It's not all bad though! Studies show we are at our most beautiful throughout our thirties so make the most of it.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Forties</i>
        <p style=\"color:#706f6f;\">Skin has all the potential to look lovely in your forties. You may still have the to-do list from your thirties but perhaps a bit more time to spend on yourself. At this stage you're looking to keep a youthful glow about your skin, staying hydrated and increasing collagen production. You may have deeper expression lines, a lack of radiance and drier or more dehydrated skin as your natural sebum production decreases.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Old &amp; Wiser</i>
        <p style=\"color:#706f6f;\">Skin in your fifties can start to change again so it's definitely the time to start taking extra care. Skin will continue to get drier, and hot flushes caused by the menopause will affect your skin throughout these years. Those with oilier skin will benefit here! As collagen and elastin decline further the structure of the skin becomes looser around the face and neck.</p>
        ";
    }

    if ($post_q8 == "3" || $post_q8 == 3){

        $post_q8_result = "
        <p><i style=\" font-style: normal;color:#706f6f;\">Studies show that 50% of women worry about their skin and how they look which is closely linked to ageing and skin health. We wanted to share what's actually happening to your skin as it ages. Don't forget that with great skincare you'll be taking steps to stem the worry and enjoy glowing, healthy skin making every day a good day.</i></p>
        <i style=\" font-style: normal;color:#706f6f;\">Forties</i>
        <p style=\"color:#706f6f;\">Skin has all the potential to look lovely in your forties. You may still have the to-do list from your thirties but perhaps a bit more time to spend on yourself. At this stage you're looking to keep a youthful glow about your skin, staying hydrated and increasing collagen production. You may have deeper expression lines, a lack of radiance and drier or more dehydrated skin as your natural sebum production decreases.</p>
        <i style=\" font-style: normal;color:#706f6f;\">Old &amp; Wiser</i>
        <p style=\"color:#706f6f;\">Skin in your fifties can start to change again so it's definitely the time to start taking extra care. Skin will continue to get drier, and hot flushes caused by the menopause will affect your skin throughout these years. Those with oilier skin will benefit here! As collagen and elastin decline further the structure of the skin becomes looser around the face and neck.</p>
        ";
    }

    echo $post_q8_result;



    $post_q9_t = $_POST['post_q9_t'];
    $post_q9_p = $_POST['post_q9_p'];       
    echo "<h1>How hydrated are you?</h1>";       
    echo $post_q9_t . "<br>";
    echo $post_q9_p . "<br>";


    $post_q11_t = $_POST['post_q11_t'];
    $post_q11_p = $_POST['post_q11_p'];  
    echo "<h1>How stressed are you feeling?</h1>";       
    echo $post_q11_t . "<br>";
    echo $post_q11_p . "<br>";

    echo "<h1>What would we find in your beauty bag?</h1>"; 

    $post_q12_reults = "<p><i style=\" font-style: normal;color:#706f6f;\">We would love to be in your beauty bag so do make use of the special gift we’ve given you and start your journey with Aurelia Probiotic Skincare.</i></p>";
    $post_q12 = split( ",", $_POST['post_q12'] );
    $post_q12_len = sizeof($post_q12) - 1;

    //
    //
    //NEED TO CHECK THESE MATCH THE CORRECT TEXT
    //
    //

    for ($i = 0; $i < $post_q12_len; $i++){


        if ($post_q12[$i] == "2" || $post_q12[$i] == 2) {

            $post_q12_reults .= "<p><i style=\" font-style: normal;color:#706f6f;\">All the basics</i></p>" .

             "<p style=\"color:#706f6f;\">You're clear on your skincare routine and you know what works best for you. We'd love you to try the core routine from Aurelia Probiotic Skincare which will hydrate and repair your skin with balancing probiotics, powerful peptides and nourishing botanicals. Why not start with the Miracle Cleanser as a great way to introduce Aurelia into your range?</p>";
        }

        if ($post_q12[$i] == "3" || $post_q12[$i] == 3) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">The latest beauty recommendations</i>" .

             "<p style=\"color:#706f6f;\">Check out our Press page to see what beauty editors and bloggers are saying about our gorgeous products! We won 28 beauty awards in 28 months including Best New Brand so we definitely have the seal of approval. Try our Cell Repair Night Oil, a multi award-winning treat for your skin and a favourite among journalists.</p>";
        }
        if ($post_q12[$i] == "4" || $post_q12[$i] == 4) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">Ethical and fair trade</i>" .

             " <p style=\"color:#706f6f;\">Make sure the brands you choose are accredited with Cruelty Free International and PETA. You should be able to see the Jumping Bunny logo on the beauty products you buy. We partner with Phytotrade Africa to ensure there is a fair trade for the communities harvesting the beautiful botanicals. Our nourishing, clinically-advanced Cell Revitalise Moisturisers are the perfect way to experience the stunning botanical ingredients we use.</p>";
        }

        if ($post_q12[$i] == "5" || $post_q12[$i] == 5) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">Mostly one brand, but all the products</i>" .

             "<p style=\"color:#706f6f;\">We love brand loyalty and know the more Aurelia products you use the better your skin will become. The full routine of stunning products starting with the cult Miracle Cleanser, Refine &amp; Polish Miracle Balm, Revitalise &amp; Glow Serum and Cell Revitalise Day Moisturiser will have your skin glowing on a daily basis. Change it up in the evening for nourished, dewy skin when you wake by adding the Cell Repair Night Oil, and Cell Revitalise Night Moisturisers. For all-over treatment try our Firm &amp; Revitalise Dry Body Oil - a scent you'll love if you've fallen for Aurelia before!</p>";
        }

        if ($post_q12[$i] == "6" || $post_q12[$i] == 6) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">Organic/BioOrganic</i>" .

             "<p style=\"color:#706f6f;\">Organic certification is great but wild-harvested botanicals are just as pure. We use ‘BioOrganic' to encompass all of the organic and wild botanicals we use and only ever use the highest quality ingredients in our products. It's not just certification you should look into, these are some of the ingredients to avoid:</p>
            <ul>
            <p style=\"color:#706f6f\">&#149; Mineral Oil - listed as paraffinum liquidum - which is a by-product of petroleum and often found in cleansers. Mineral oil leaves a greasy residue which sits on the skin and can cause breakouts.</p>
            <p style=\"color:#706f6f\">&#149; Sulfates or SLS - listed as sodium laurel sulfate or sodium laureth sulfate - are synthetic foaming agents found in thousands of products which strip the skin of its natural oils and upset the acid mantel, causing sensitivity and dryness.</p>
            <p style=\"color:#706f6f\">&#149; MI or MIT - listed as Methylisothiazolinone - is a harsh preservative known to cause skin irritation.</p>
            <p style=\"color:#706f6f\">&#149; Synthetic Fragrance - sometimes listed as parfum - can be made up of hundreds of ingredients which aren't all visible and could cause sensitivity. Look instead for natural fragrances like essential oils or flower waters which will be listed in full.</p>
            </ul>
            <p style=\"color:#706f6f\">The Miracle Cleanser and Refine &amp; Polish Miracle Balm are a great way to experience the power of free-from cleansing and exfoliating products - you'll never look back!</p>";
        }

        if ($post_q12[$i] == "7" || $post_q12[$i] == 7) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">Anti-Ageing/New Science</i>" .

             "<p style=\"color:#706f6f;\">Check out the science page of our website - you'll see detailed data and results from trials which prove our age-preventative results. Experience the power of probiotics to calm inflammation and peptides to boost natural collagen production. We pride ourselves on a fusion of advanced scientifically proven technology and natural luxury and think you'll love our Revitalise &amp; Glow Serum for its concentrated levels of probiotics, peptides and antioxidants which firm, nourish and repair the skin.</p>";
        }

        if ($post_q12[$i] == "8" || $post_q12[$i] == 8) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">Textures/scent</i>" .

             "<p style=\"color:#706f6f;\">We fuse next-generation probiotic technology with ethically sourced 100% BioOrganic botanicals for visible results and stunning blends and textures. Each product has been designed to absorb beautifully into the skin, depositing the purest ingredients where they are most needed. If you love skincare that smells incredible then Aurelia is definitely the range for you. Try the Cell Repair Night Oil and you'll be hooked instantly on the blend of neroli, lavender, rose and mandarin.</p>";
        }

        if ($post_q12[$i] == "9" || $post_q12[$i] == 9) {

            $post_q12_reults .= "<i style=\" font-style: normal;color:#706f6f;\">My Go-To Classics</i>" .

             "<p style=\"color:#706f6f;\">We know how important it is to find skincare you can rely on time and time again. That's why we only use the finest botanicals, balancing probiotics and powerful peptides which are proven to help repair, rebalance and make your skin glow. There are certain products that are a staple in your beauty bag and we'd love to be a part of yours. We think our Miracle Cleanser will soon be a classic in your eyes and with 30 awards for the range in 30 months we do dream of cult classic status!</p>";
        }

    }

    echo $post_q12_reults;

    $post_q13_t = $_POST['post_q13_t'];
    $post_q13_p = $_POST['post_q13_p'];  
    echo "<h1>Do ingredients matter to you?</h1>";    
    echo $post_q13_t . "<br>";
    // echo $post_q13_p . "<br>";

    if (  $post_q13_p  == "p-with-li-tags"){
    
        $post_q13_p  = "
        <p style=\"color:#706f6f\">At Aurelia we are all about an honest and clear ingredients policy so you can be safe in the knowledge that the ingredients we use are the highest quality and most beneficial for you. Our range is free from a number of harmful and unethical ingredients which include: synthetic fragrances, colorants, parabens, mineral oils, silicones, sulphates, propylene glycol, phthalates, GMO, PEGs, TEA, DEA, MI, formaldehyde, urea, mercury, lead and bee venom. Some of the ingredients we are most passionate about avoiding include:</p>
        <ul>
        <p style=\"color:#706f6f\">&#149; MI or MIT - listed as Methylisothiazolinone - is a harsh preservative known to cause skin irritatio</p>
        <p style=\"color:#706f6f\">&#149; Sulfates or SLS - visible as sodium laurel sulfate or sodium laureth sulfate - are synthetic foaming agents found in thousands of products which strip the skin of its natural oils and upset the acid mantel, causing sensitivity and dryness.</p>
        <p style=\"color:#706f6f\">&#149; Synthetic Fragrance - sometimes listed as parfum - can be made up of hundreds of ingredients which aren't all visible and could cause sensitivity. Look instead for natural fragrances like essential oils or flower waters which will often be listed in full.</p>
        </ul>
        <p style=\"color:#706f6f\">For a lot more detail on these ingredients please visit our website.</p>";
    }else{

        echo $post_q13_p . "<br>";
    }

    $post_q14_t = $_POST['post_q14_t'];
    $post_q14_p = $_POST['post_q14_p'];  
    echo "<h1>How does your day end?</h1>";             
    echo $post_q14_t . "<br>";
    echo $post_q14_p . "<br>";


    // Setup recipients
    // $to = 'romero@wallacehealth.co.uk';
    $to = $email;

    // subject

    // message (to use single quotes in the 'message' variable, supercede them with a back slash like this-->&nbsp; \'

$message = '


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Aurelia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<style>img{border:0px}</style>
</head>

<body style="margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td align="center" style="padding: 10px 0 0px 0; color: #706f6f; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                        <img src="http://aureliaskincare.com/skintools/email/images/resultsemail_header.jpg">
                        </td>
                    </tr>
                    <tr>
                        <td  style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #706f6f; font-family: Arial, sans-serif; font-size: 24px; text-align:center;">
                                        <b></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #706f6f; font-family: Arial, sans-serif; line-height: 20px; text-align:center;">
                                        <p>Thank you so much for taking time out to tell us a bit more about you, your skin and lifestyle. Based on some of the things you told us, we have combined our suggestions and top tips alongside &pound;10.00* off our tailored recommended products to get you started with the Aurelia products we think your skin will love.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 0 30px 0; color: #706f6f; font-family: Arial, sans-serif; line-height: 20px; text-align:center;">
                                        <p>' . $_POST['post_0'] . '</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="260" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td>
                                                                <img src="http://aureliaskincare.com/skintools/email/images/musthave_2.png" alt="" width="200" height="51" style="display: block;margin-left: auto;     margin-right: auto;" />
                                                                <a href="' . $recommended_img_m_link . '">
                                                                    <img src="' . $musthave_img . '" alt="" width="200" height="250" style="display: block; text-align:center; margin-left:auto;margin-right:auto;" />
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 25px 0 0 0; color: #706f6f; font-family: Arial, sans-serif; line-height: 20px; text-align:center;">
                                                                <b>' . $musthave_t . '</b>
                                                                <br> ' . $musthave_p . '
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="font-size: 0; line-height: 0;" width="20">
                                                    &nbsp;
                                                </td>
                                                <td width="260" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td>
                                                                <img src="http://aureliaskincare.com/skintools/email/images/recommended_2.png" alt="" width="200" height="51" style="display: block;margin-left: auto;     margin-right: auto;" />
                                                                <a href="' . $recommended_img_r_link . '">
                                                                    <img src="' . $recommended_img . '" alt="" width="200" height="250" style="display: block; text-align:center; margin-left:auto;margin-right:auto;" />
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 25px 0 0 0; color: #706f6f; font-family: Arial, sans-serif; line-height: 20px; text-align:center;">
                                                                <b>' . $recommended_t . '</b>
                                                                <br> ' . $recommended_p . '
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <table style="width:100%; text-align:center;">
                                    <tr>
                                        <td>
                                            <p id="musthave_data">
                                                <span id="price" style="color:#706f6f;">&pound;'.$price1.'</span><span style="color:#706f6f;"> | </span><span id="size" style="color:#706f6f;">'.$size1.'ml</span>
                                            </p>
                                            <a href="' . $recommended_img_m_link . '">
                                                <img src="http://aureliaskincare.com/skintools/email/images/10off.png" width="168" height="60" style="display: block; text-align:center; margin-left:auto;margin-right:auto;" />
                                            </a>
                                        </td>
                                        <td>
                                            <p id="musthave_data">
                                                <span id="price" style="color:#706f6f;">&pound;'.$price2.'</span><span style="color:#706f6f;"> | </span><span id="size" style="color:#706f6f;">'.$size2.'ml</span>
                                            </p>
                                            <a href="' . $recommended_img_r_link . '">
                                                <img src="http://aureliaskincare.com/skintools/email/images/10off.png" width="168" height="60" style="display: block; text-align:center; margin-left:auto;margin-right:auto;" />
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <tr>
                                    <td style="padding: 0px 30px 10px 0px;">
                                        <p style="color:#706f6f; text-align:center;">*Select your \'Must Have\' or \'Recommended\' Aurelia product above for &pound;10.00 to be deducted from your next order.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: -' . $post_q2_height . 'px 30px 30px 30px;">
                                        <b style="color:#706f6f;text-transform: uppercase;">' . $post_q2_title . '</b>
                                        <br>
                                        <p style="color:#706f6f;"></p>
                                        ' . $begin_html_tag . '
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <b style="text-transform: uppercase;color:#706f6f;">HOW DO YOU FEEL?</b>
                                        <br>
                                        <p style="color:#706f6f;"> ' . $post_q3_t . '
                                        </p>
                                        <p style="color:#706f6f;"> ' . $post_q3_p . ' </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <br>
                                        <b style="text-transform: uppercase;color:#706f6f;">WHAT&#39;S YOUR MORNING SKINCARE ROUTINE?</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q4_t . '
                                        </p>
                                      <p style="color:#706f6f;">   ' . $post_q4_p . ' </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <br>
                                        <b style="text-transform: uppercase;color:#706f6f;">THE THINGS YOU DON\'T LOVE ABOUT YOUR SKIN</b>
                                        <br> ' . $post_q6_reults .'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <b style="text-transform: uppercase;color:#706f6f;">HOW YOU\'D LOVE TO LOOK</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q7_t . '
                                        </p>
                                        <p style="color:#706f6f;">  ' . $post_q7_p . ' </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <br>
                                        <b style="text-transform: uppercase;color:#706f6f;">WE DON\'T OFTEN GET ASKED HOW OLD WE THINK WE LOOK</b>
                                        <br> ' . $post_q8_result .'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <b style="text-transform: uppercase;color:#706f6f;">HOW HYDRATED ARE YOU</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q9_t . '
                                        </p>
                                        <p style="color:#706f6f;">  ' . $post_q9_p . '</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <br>
                                        <b style="text-transform: uppercase;color:#706f6f;">HOW STRESSED ARE YOU FEELING?</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q11_t . '
                                        </p>
                                       <p style="color:#706f6f;">  ' . $post_q11_p . '</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <br>
                                        <b style="text-transform: uppercase;color:#706f6f;">WHAT WOULD WE FIND IN YOUR BEAUTY BAG?</b>
                                        <br> ' . $post_q12_reults . '
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px;">
                                        <b style="text-transform: uppercase;color:#706f6f;">DO INGREDIENTS MATTER TO YOU?</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q13_t . '
                                        </p>
                                         <p style="color:#706f6f;">  ' . $post_q13_p . '</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 0px 30px;">
                                        <b style="text-transform: uppercase;color:#706f6f;">HOW DOES YOUR DAY END?</b>
                                        <br>
                                        <p style="color:#706f6f;">
                                            ' . $post_q14_t . '
                                        </p>
                                        <p style="color:#706f6f;">' . $post_q14_p . '</p>
                                    </td>
                                </tr>
								 <tr>
							<td style="">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #706f6f; font-family: Arial, sans-serif; text-align:center;" width="100%">
                                        <img src="http://aureliaskincare.com/skintools/email/images/morehelp.png">
                                        <p> If you have any more questions or would like further skincare advice, please contact the Aurelia office by emailing
                                            <a href="mailto:customerservices@aureliaskincare.com" style="color: #706f6f;">customerservices@aureliaskincare.com</a> or call 0207 751 0022. </p>
                                        <p>
                                            Office hours are from Monday - Friday 9am-5:30pm GMT
                                        </p>
                                        <a href="'.$storeUrl.'">
                                        <img src="http://aureliaskincare.com/skintools/email/images/backto.png">
                                        <img src="http://aureliaskincare.com/skintools/email/images/resultsemail_footer_2.jpg">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
                            </table>
                        </td>
                    </tr>
                   
    </table>
</body>

</html>


';

    // To send HTML mail, the Content-type header must be set 
    // Additional headers

    $subject  = 'Thank you so much for taking time out to tell us a bit more about you, your skin and lifestyle...';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From:Aurelia <antonia@aureliaSkincare.com' . "\r\n";
    // Send the email
    $message = html_entity_decode($message, ENT_QUOTES, "UTF-8");
    //mail($to, $subject, $message, $headers);
	$fromEmail = "antonia@aureliaSkincare.com"; // sender email address
	$fromName = "Aurelia"; // sender name
	$config = array('auth' => 'login',
                'username' => 'bladerunner01',
                'password' => 'andy33333055');
				
	$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
	
	$mail = new Zend_Mail('utf-8');
	$mail->setBodyText($message);
	$mail->setBodyHtml($message);
	$mail->setFrom($fromEmail, $fromName);
	$mail->addTo($to, $to);
	$mail->setSubject($subject);
	try {
		$mail->send($transport);
	}catch(Exception $ex) {
		// I assume you have your custom module. 
		// If not, you may keep 'customer' instead of 'yourmodule'.
		echo 'Unable to send email.';
	}
?>