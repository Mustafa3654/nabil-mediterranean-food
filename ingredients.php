<?php
include "includes/connection.php";

// Validate and normalize item id from query string.
if (!isset($_GET["item"]) || !ctype_digit((string)$_GET["item"])) {
    header("Location: index");
    exit;
}

$itemId = (int)$_GET["item"];
// Use prepared statement to avoid SQL injection on item lookup.
$stmt = $conn->prepare("SELECT item_name, Ingredients, item_pic FROM items WHERE item_id = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

include "includes/header.php";
?>

<div class="container">
    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                echo "<h4>Ingredients of: <br> &emsp;" . htmlspecialchars($row["item_name"]) . " </h4>";
                $ingredients = $row["Ingredients"];
                $ing = array_map('trim', explode(",", $ingredients));
                $listItems = "";
                foreach ($ing as $item) {
                    $listItems .= "<li class='list-group-item'>" . htmlspecialchars($item) . "</li>\n";
                }
                $unorderedList = "<ul class='list-group list-group-flush'>\n" . $listItems . "</ul>";
                echo $unorderedList;
                ?>
                <div class="col-3">
                                    <img src="<?php echo htmlspecialchars($row['item_pic']); ?>" alt="" loading="lazy" style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                </div>
                                <?php
            }
        }
        else{
            echo "No Ingredients Found!";
        }
        $stmt->close();
    ?>
</div>

<?php
include "includes/footer.php"
?>


