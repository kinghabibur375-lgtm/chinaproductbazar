<?php
require 'config.php';

if (isset($_POST['query'])) {
    $search = "%" . trim($_POST['query']) . "%";
    
    // Search for matching products (Limit 5 for speed)
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE ? LIMIT 5");
    $stmt->execute([$search]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        foreach ($results as $row) {
            $img = !empty($row['image']) ? 'uploads/'.$row['image'] : 'uploads/no-img.jpg';
            echo '
            <a href="product_details.php?id='.$row['id'].'" class="search-item">
                <img src="'.$img.'" alt="img">
                <div class="search-info">
                    <div class="search-title">'.htmlspecialchars($row['title']).'</div>
                    <div class="search-price">৳ '.number_format($row['price_bdt']).'</div>
                </div>
            </a>';
        }
        // "View All" Link
        echo '<a href="index.php?search='.htmlspecialchars($_POST['query']).'" class="search-view-all">View all results for "'.htmlspecialchars($_POST['query']).'"</a>';
    } else {
        echo '<div class="search-no-result">No products found.</div>';
    }
}
?>