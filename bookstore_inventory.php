<?php
header("Content-Type: text/plain; charset=UTF-8");

# Bookstore inventory example.

# Starting list of books.
$inventory = array(
	array(
		"title" => "The Great Gatsby",
		"author" => "F. Scott Fitzgerald",
		"price" => 10.99,
		"quantity" => 12,
	),
	array(
		"title" => "1984",
		"author" => "George Orwell",
		"price" => 9.50,
		"quantity" => 8,
	),
	array(
		"title" => "To Kill a Mockingbird",
		"author" => "Harper Lee",
		"price" => 11.25,
		"quantity" => 10,
	),
	array(
		"title" => "Pride and Prejudice",
		"author" => "Jane Austen",
		"price" => 8.75,
		"quantity" => 6,
	),
	array(
		"title" => "The Hobbit",
		"author" => "J.R.R. Tolkien",
		"price" => 12.40,
		"quantity" => 15,
	),
);

# Add one book to the inventory.
function addBook ($inventory, $title, $author, $price, $quantity) {
				# Build the new book record.
    $newBook = array(
        "title" => $title,
        "author" => $author,
        "price" => $price,
        "quantity" => $quantity,
    );

				# Append the new book.
    array_push($inventory, $newBook);

				# Return the updated inventory.
    return $inventory;
}


# Remove a book by title.
function removeBook ($inventory, $title) {
	# Check each book and keep its index.
    foreach ($inventory as $index => $book) {
        if ($book["title"] === $title) {
			# Remove the matching book.
            array_splice($inventory, $index, 1);
            break;
        }
    }

	# Return the updated inventory.
    return $inventory;
}

# Change the quantity of one book.
function updateQuantity ($inventory, $title, $newQuantity) {
	# Use a reference so the original array is updated.
    foreach ($inventory as &$book) {
        if ($book["title"] === $title) {
			# Set the new stock amount.
            $book["quantity"] = $newQuantity;
            break;
        }
    }

	# Return the updated inventory.
    return $inventory;
}

# Sort books by a selected field.
function sortInventory($inventory, $sortBy) {
				# Sort in ascending order.
    usort($inventory, function($a, $b) use ($sortBy) {
        return $a[$sortBy] <=> $b[$sortBy];
    });

				# Return the sorted inventory.
    return $inventory;
}

# Print a label and the current inventory.
function displayInventory($label, $inventory) {
	echo str_repeat("=", 40) . "\n";
	echo $label . "\n";
	echo str_repeat("-", 40) . "\n";
	print_r($inventory);
	echo "\n";
}

# Show the starting inventory.
displayInventory("Initial inventory:", $inventory);

# Add a new book.
$inventory = addBook($inventory, "Moby-Dick", "Herman Melville", 13.20, 5);
displayInventory("After adding Moby-Dick:", $inventory);

# Add another new book.
$inventory = addBook($inventory, "Brave New World", "Aldous Huxley", 10.75, 9);
displayInventory("After adding Brave New World:", $inventory);

# Remove one book.
$inventory = removeBook($inventory, "1984");
displayInventory("After removing 1984:", $inventory);

# Update The Hobbit stock.
$inventory = updateQuantity($inventory, "The Hobbit", 18);
displayInventory("After updating The Hobbit quantity:", $inventory);

# Update Pride and Prejudice stock.
$inventory = updateQuantity($inventory, "Pride and Prejudice", 11);
displayInventory("After updating Pride and Prejudice quantity:", $inventory);

# Sort by title.
$inventory = sortInventory($inventory, "title");
displayInventory("After sorting by title:", $inventory);

# Sort by price.
$inventory = sortInventory($inventory, "price");
displayInventory("After sorting by price:", $inventory);

# Sort by quantity.
$inventory = sortInventory($inventory, "quantity");
displayInventory("After sorting by quantity:", $inventory);

