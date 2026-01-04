<?php
/**
 * Seed Products - Add 10 products for each category
 */
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Get all categories
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        echo "No categories found. Please create categories first.\n";
        exit(1);
    }
    
    echo "Found " . count($categories) . " categories.\n\n";
    
    // Sample product data templates
    $productTemplates = [
        'Reels Bundle' => [
            ['Premium Instagram Reels Pack 2025', 'Get 50+ premium Instagram reels templates, trending transitions, and music suggestions. Perfect for content creators.', 299, 20, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Viral TikTok Reels Collection', 'Collection of 30 viral TikTok reels templates with trending effects and transitions. Boost your engagement!', 199, 15, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
            ['Instagram Stories Reels Pack', 'Professional Instagram stories and reels templates. Includes animations, text overlays, and filters.', 249, 18, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
            ['Trending Reels Bundle रु99', 'Affordable reels bundle with 20 trending templates. Perfect for beginners!', 99, 10, 'https://images.unsplash.com/photo-1562577309-4932fdd64cd1?w=400'],
            ['Reels Bundle रु149', 'Mid-range reels bundle with 35 professional templates and music suggestions.', 149, 12, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Reels Bundle रु199', 'Premium reels bundle with 45 templates, transitions, and exclusive effects.', 199, 25, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
            ['Instagram Reels Master Pack', 'Complete Instagram reels package with 60+ templates, captions, and hashtag suggestions.', 349, 30, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
            ['Combo Reels Bundle', 'Special combo pack with Instagram and TikTok reels templates (80+ templates).', 399, 35, 'https://images.unsplash.com/photo-1562577309-4932fdd64cd1?w=400'],
            ['Wedding Reels Template Pack', 'Beautiful wedding reels templates with romantic transitions and music suggestions.', 279, 20, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400'],
            ['Business Reels Professional Pack', 'Professional business reels templates for brands and businesses. Includes logo animations.', 329, 25, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400']
        ],
        'WhatsApp Templates' => [
            ['WhatsApp Status Pack 2025', '50+ premium WhatsApp status templates with quotes, wishes, and festive designs.', 149, 15, 'https://images.unsplash.com/photo-1611605698323-b1e99cfd37ea?w=400'],
            ['Good Morning WhatsApp Status', 'Beautiful good morning status templates with inspirational quotes and designs.', 99, 10, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=400'],
            ['Festival WhatsApp Status Pack', 'Complete festival status templates for Diwali, Holi, Christmas, and more.', 199, 20, 'https://images.unsplash.com/photo-1605296867304-46d5465a13f1?w=400'],
            ['Birthday WhatsApp Status Collection', '20+ birthday status templates with animations and personalized designs.', 129, 12, 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=400'],
            ['Love & Romance Status Pack', 'Romantic WhatsApp status templates for couples with heart designs.', 149, 15, 'https://images.unsplash.com/photo-1518621012428-6c618bd77135?w=400'],
            ['Motivational Quotes Status Pack', '50+ motivational and inspirational quotes status templates.', 179, 18, 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400'],
            ['Business WhatsApp Status Templates', 'Professional business status templates for promotions and announcements.', 199, 20, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
            ['Funny Memes WhatsApp Status', '30+ funny meme templates for WhatsApp status. Keep your friends laughing!', 99, 10, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Wedding WhatsApp Status Pack', 'Elegant wedding status templates for brides and grooms.', 249, 25, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400'],
            ['Video Status Templates Pack', 'Animated video status templates for WhatsApp. Stand out from the crowd!', 279, 28, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400']
        ],
        'Digital Planner' => [
            ['2025 Digital Planner Pro', 'Complete digital planner for 2025 with calendars, to-do lists, and goal tracking.', 299, 20, 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400'],
            ['Daily Planner Template Pack', 'Beautiful daily planner templates for organizing your day and tasks.', 149, 15, 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=400'],
            ['Budget Planner Digital Notebook', 'Complete budget planner with expense tracking, savings goals, and financial planning.', 199, 18, 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400'],
            ['Fitness & Health Planner', 'Digital fitness planner with workout schedules, meal planning, and progress tracking.', 249, 22, 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400'],
            ['Student Study Planner', 'Comprehensive study planner for students with exam schedules and study trackers.', 179, 16, 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400'],
            ['Business Planner & Organizer', 'Professional business planner with project management and goal setting templates.', 329, 25, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
            ['Meal Planning Digital Planner', 'Weekly meal planning templates with shopping lists and recipe organizers.', 199, 18, 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400'],
            ['Wedding Planning Digital Book', 'Complete wedding planning organizer with timeline, checklist, and vendor tracking.', 349, 30, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400'],
            ['Habit Tracker Planner', 'Daily habit tracker with weekly and monthly review templates.', 149, 15, 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=400'],
            ['Creative Journal Digital Pack', 'Beautiful journal templates for creative writing, gratitude, and self-reflection.', 179, 16, 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=400']
        ],
        'Social Media Pack' => [
            ['Complete Social Media Content Pack', '100+ social media templates for Instagram, Facebook, and Twitter posts.', 399, 30, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Instagram Post Template Bundle', '50+ Instagram post templates with different styles and layouts.', 249, 20, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
            ['Facebook Cover Photo Pack', 'Professional Facebook cover photos and profile picture templates.', 199, 18, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
            ['Twitter Header Templates', 'Customizable Twitter header templates for personal and business profiles.', 149, 15, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['LinkedIn Banner Templates', 'Professional LinkedIn banner templates for job seekers and professionals.', 179, 16, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
            ['YouTube Thumbnail Templates', 'Eye-catching YouTube thumbnail templates for better click-through rates.', 299, 25, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
            ['Pinterest Pin Templates', 'Beautiful Pinterest pin templates optimized for engagement and clicks.', 249, 22, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Instagram Story Templates Pack', '100+ Instagram story templates with animations and interactive elements.', 329, 28, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
            ['Social Media Quote Cards', 'Ready-to-use quote cards for Instagram, Facebook, and Twitter.', 149, 15, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
            ['Brand Identity Social Pack', 'Complete social media branding pack with logos, color schemes, and templates.', 449, 35, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400']
        ],
        'Video Templates' => [
            ['Premium Video Intro Templates', 'Professional video intro templates with animations and music suggestions.', 349, 25, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
            ['YouTube Outro Templates', 'Eye-catching YouTube outro templates to increase subscribers and engagement.', 299, 22, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
            ['Wedding Video Templates', 'Romantic wedding video templates with transitions and effects.', 399, 30, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400'],
            ['Corporate Video Templates', 'Professional corporate video templates for businesses and presentations.', 449, 32, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
            ['Travel Vlog Templates', 'Stunning travel vlog templates with cinematic transitions.', 329, 28, 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400'],
            ['Product Showcase Video Pack', 'Professional product showcase video templates for e-commerce.', 379, 30, 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400'],
            ['Music Video Templates', 'Dynamic music video templates with visual effects and animations.', 299, 25, 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?w=400'],
            ['Promotional Video Templates', 'Eye-catching promotional video templates for marketing campaigns.', 349, 28, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
            ['Tutorial Video Templates', 'Professional tutorial video templates with step-by-step animations.', 279, 22, 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=400'],
            ['YouTube Channel Branding Pack', 'Complete YouTube channel branding with intro, outro, and transition templates.', 449, 35, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400']
        ]
    ];
    
    // Default template for categories not in the list
    $defaultTemplates = [
        ['Premium Product Pack 1', 'High-quality digital product with professional design and easy-to-use templates.', 199, 15, 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400'],
        ['Premium Product Pack 2', 'Complete digital solution for your needs with comprehensive features.', 249, 20, 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=400'],
        ['Premium Product Pack 3', 'Professional-grade templates and resources for maximum impact.', 299, 25, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
        ['Standard Product Pack 1', 'Quality digital product at an affordable price point.', 149, 10, 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400'],
        ['Standard Product Pack 2', 'Great value product with essential features and templates.', 179, 15, 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=400'],
        ['Basic Product Pack 1', 'Entry-level product perfect for beginners.', 99, 5, 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=400'],
        ['Basic Product Pack 2', 'Simple and effective solution for your digital needs.', 129, 8, 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400'],
        ['Deluxe Product Pack', 'Premium package with exclusive features and bonus content.', 349, 30, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400'],
        ['Ultimate Collection Pack', 'The complete bundle with everything you need in one package.', 449, 35, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'],
        ['Starter Pack', 'Perfect starter package to get you started on your journey.', 79, 5, 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400']
    ];
    
    $totalAdded = 0;
    $pdo->beginTransaction();
    
    foreach ($categories as $category) {
        $categoryName = $category['name'];
        $categoryId = $category['id'];
        
        echo "Adding products for category: {$categoryName} (ID: {$categoryId})\n";
        
        // Get templates for this category or use default
        $templates = $productTemplates[$categoryName] ?? $defaultTemplates;
        
        // Take only 10 products
        $templates = array_slice($templates, 0, 10);
        
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, discount, category_id, image, product_link, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        
        foreach ($templates as $index => $template) {
            list($title, $description, $price, $discount, $image) = $template;
            
            // Create a sample product link (you should replace these with actual links)
            $productLink = "https://drive.google.com/file/d/sample_product_" . strtolower(str_replace(' ', '_', $title)) . "/view";
            
            try {
                $stmt->execute([
                    $title,
                    $description,
                    $price,
                    $discount,
                    $categoryId,
                    $image,
                    $productLink
                ]);
                
                $totalAdded++;
                echo "  ✓ Added: {$title} (रु{$price})\n";
            } catch (PDOException $e) {
                // Skip if product already exists (duplicate title)
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    echo "  ⊗ Skipped (already exists): {$title}\n";
                } else {
                    throw $e;
                }
            }
        }
        
        echo "\n";
    }
    
    $pdo->commit();
    
    echo "========================================\n";
    echo "✓ Successfully added {$totalAdded} products!\n";
    echo "========================================\n";
    echo "\nNote: Product download links are sample links. Please update them with actual download links in the admin panel.\n";
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}





