<?php

declare(strict_types=1);

#
# Detect web requests reliably (including built-in PHP server).
#
function isWebRequest(): bool
{
	if (PHP_SAPI !== "cli") {
		return true;
	}

	return isset($_SERVER["REQUEST_METHOD"])
		|| isset($_SERVER["HTTP_HOST"])
		|| isset($_SERVER["REMOTE_ADDR"])
		|| isset($_SERVER["SERVER_PROTOCOL"]);
}

#
# Start a readable HTML shell for web requests.
#
function beginWebOutput(): void
{
	if (!isWebRequest()) {
		return;
	}

	echo "<!doctype html>\n";
	echo "<html lang=\"en\">\n";
	echo "<head>\n";
	echo "<meta charset=\"utf-8\">\n";
	echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
	echo "<title>Inventory Management Output</title>\n";
	echo "<style>\n";
	echo "body { margin: 0; background: #f3f6fb; color: #1f2937; font-family: 'Segoe UI', Tahoma, sans-serif; }\n";
	echo ".wrap { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }\n";
	echo ".card { background: #ffffff; border: 1px solid #dbe3ef; border-radius: 12px; box-shadow: 0 6px 24px rgba(31, 41, 55, 0.08); }\n";
	echo ".title { margin: 0; padding: 1rem 1.25rem; border-bottom: 1px solid #e5eaf2; font-size: 1.1rem; }\n";
	echo ".controls { padding: 1rem 1.25rem; border-bottom: 1px solid #e5eaf2; background: #f8fbff; }\n";
	echo ".controls p { margin: 0 0 0.75rem 0; font-size: 0.92rem; color: #334155; }\n";
	echo ".grid { display: grid; grid-template-columns: repeat(4, minmax(120px, 1fr)); gap: 0.5rem; }\n";
	echo ".grid input, .grid select, .grid button { width: 100%; box-sizing: border-box; padding: 0.5rem; border: 1px solid #cdd8e7; border-radius: 8px; font-size: 0.9rem; }\n";
	echo ".grid button { background: #0f766e; color: #fff; border-color: #0f766e; cursor: pointer; }\n";
	echo ".quick { margin-top: 0.6rem; display: flex; flex-wrap: wrap; gap: 0.45rem; }\n";
	echo ".quick a { text-decoration: none; font-size: 0.82rem; padding: 0.4rem 0.55rem; border-radius: 999px; border: 1px solid #c9d9ee; color: #0f3f66; background: #eaf4ff; }\n";
	echo ".quick a:hover { background: #d9ecff; }\n";
	echo "pre { margin: 0; padding: 1rem 1.25rem; overflow-x: auto; font: 14px/1.45 Consolas, Monaco, 'Courier New', monospace; }\n";
	echo "</style>\n";
	echo "</head>\n";
	echo "<body><div class=\"wrap\"><div class=\"card\"><h1 class=\"title\">Inventory Management Report</h1>";
	echo "<div class=\"controls\">";
	echo "<p>Web test form: try invalid values or unknown item names to verify error handling.</p>";
	echo "<form method=\"get\" class=\"grid\">";
	echo "<select name=\"test_op\">";
	echo "<option value=\"update\">Update Quantity</option>";
	echo "<option value=\"sale\">Record Sale</option>";
	echo "<option value=\"discount\">Apply Discount</option>";
	echo "<option value=\"lowstock\">Low Stock Check</option>";
	echo "</select>";
	echo "<input type=\"text\" name=\"item\" placeholder=\"Item name (for update/sale)\">";
	echo "<input type=\"text\" name=\"value\" placeholder=\"Value (qty/units/%/threshold)\">";
	echo "<button type=\"submit\">Run Test</button>";
	echo "</form>";
	echo "<div class=\"quick\">";
	echo "<a href=\"?test_op=update&item=GhostItem&value=5\">Not Found Item</a>";
	echo "<a href=\"?test_op=update&item=Mouse&value=-2\">Negative Quantity</a>";
	echo "<a href=\"?test_op=discount&value=150\">Invalid Discount</a>";
	echo "<a href=\"?test_op=lowstock&value=-1\">Invalid Threshold</a>";
	echo "</div>";
	echo "</div><pre>";
}

#
# Close the HTML shell for web requests.
#
function endWebOutput(): void
{
	if (!isWebRequest()) {
		return;
	}

	echo "</pre></div></div></body></html>\n";
}

#
# Inventory Management Demo (Improved Version)
# This script demonstrates:
# - Typed functions and stricter validation
# - Case-insensitive item lookup
# - foreach, while, do-while, and for loops
# - Optional JSON persistence
# - Best-selling report from sales activity
#

# 1) Sample inventory data: item => [quantity, price]
$inventory = [
	"Laptop" => ["quantity" => 10, "price" => 899.99],
	"Mouse" => ["quantity" => 35, "price" => 24.50],
	"Keyboard" => ["quantity" => 18, "price" => 49.99],
	"Monitor" => ["quantity" => 7, "price" => 199.99],
	"USB Cable" => ["quantity" => 50, "price" => 8.75],
	"Headset" => ["quantity" => 4, "price" => 79.95],
];

# Track sold units per item for the victory chart.
$salesLog = [];

#
# Display inventory table and totals.
#
function displayInventory(array $inventory, string $title = "Current Inventory"): void
{
	echo "\n=== {$title} ===\n";

	if (empty($inventory)) {
		echo "Inventory is empty.\n";
		return;
	}

	printf("%-15s %-10s %-10s %-12s\n", "Item", "Quantity", "Price", "Total Value");
	echo str_repeat("-", 52) . "\n";

	$grandTotal = 0.0;

	# Loop through each inventory item and compute totals.
	foreach ($inventory as $itemName => $details) {
		$quantity = (int) ($details["quantity"] ?? 0);
		$price = (float) ($details["price"] ?? 0);
		$itemTotal = $quantity * $price;
		$grandTotal += $itemTotal;

		printf("%-15s %-10d $%-9.2f $%-11.2f\n", $itemName, $quantity, $price, $itemTotal);
	}

	echo str_repeat("-", 52) . "\n";
	printf("%-37s $%-11.2f\n", "Entire Inventory Value:", $grandTotal);
}

#
# Return the real inventory key for a user-provided item name.
# This allows case-insensitive lookups like "mouse", "Mouse", or "MOUSE".
#
function findItemKey(array $inventory, string $itemName): ?string
{
	$needle = strtolower(trim($itemName));
	if ($needle === "") {
		return null;
	}

	$keys = array_keys($inventory);
	$index = 0;

	while ($index < count($keys)) {
		$currentKey = $keys[$index];
		if (strtolower($currentKey) === $needle) {
			return $currentKey;
		}
		$index++;
	}

	return null;
}

#
# Update the quantity of an existing item.
# Returns true on success, false on failure.
#
function updateItemQuantity(array &$inventory, string $itemName, int $newQuantity): bool
{
	if ($newQuantity < 0) {
		echo "Error: Quantity cannot be negative for '{$itemName}'.\n";
		return false;
	}

	$realKey = findItemKey($inventory, $itemName);
	if ($realKey === null) {
		echo "Warning: Item '{$itemName}' was not found. No update made.\n";
		return false;
	}

	$oldQuantity = $inventory[$realKey]["quantity"];
	$inventory[$realKey]["quantity"] = $newQuantity;

	echo "Updated '{$realKey}' quantity from {$oldQuantity} to {$newQuantity}.\n";
	return true;
}

#
# Record a sale by reducing stock, with stock-safety checks.
#
function recordSale(array &$inventory, array &$salesLog, string $itemName, int $units): bool
{
	if ($units <= 0) {
		echo "Error: Sale units must be greater than zero for '{$itemName}'.\n";
		return false;
	}

	$realKey = findItemKey($inventory, $itemName);
	if ($realKey === null) {
		echo "Warning: Cannot record sale. Item '{$itemName}' not found.\n";
		return false;
	}

	$currentQty = (int) $inventory[$realKey]["quantity"];
	if ($units > $currentQty) {
		echo "Error: Not enough '{$realKey}' stock for sale of {$units}. Available: {$currentQty}.\n";
		return false;
	}

	$inventory[$realKey]["quantity"] = $currentQty - $units;
	$salesLog[$realKey] = ($salesLog[$realKey] ?? 0) + $units;
	echo "Sale recorded: {$units} units of '{$realKey}'. Remaining stock: {$inventory[$realKey]["quantity"]}.\n";
	return true;
}

#
# Find and display low-stock items below a threshold.
#
function lowStockAlert(array $inventory, int $threshold): void
{
	echo "\n=== Low Stock Alert (threshold: {$threshold}) ===\n";

	if ($threshold < 0) {
		echo "Error: Threshold cannot be negative.\n";
		return;
	}

	$foundAny = false;

	# Classic for-loop pass to keep loop variety in the exercise.
	$keys = array_keys($inventory);
	for ($i = 0; $i < count($keys); $i++) {
		$itemName = $keys[$i];
		$quantity = (int) ($inventory[$itemName]["quantity"] ?? 0);

		if ($quantity < $threshold) {
			echo "- {$itemName}: {$quantity} in stock\n";
			$foundAny = true;
		}
	}

	if (!$foundAny) {
		echo "No low-stock items found.\n";
	}
}

#
# Apply discount to every item's price.
# Uses do-while loop and prints old/new prices.
#
function applyDiscount(array &$inventory, float $discountPercent): bool
{
	echo "\n=== Applying Discount: {$discountPercent}% ===\n";

	if ($discountPercent < 0 || $discountPercent > 100) {
		echo "Error: Discount percent must be between 0 and 100.\n";
		return false;
	}

	if (empty($inventory)) {
		echo "Inventory is empty. No discount applied.\n";
		return false;
	}

	$factor = (100 - $discountPercent) / 100;
	$keys = array_keys($inventory);
	$index = 0;

	# do-while guarantees one pass, even if the array is tiny.
	do {
		$itemName = $keys[$index];
		$oldPrice = (float) $inventory[$itemName]["price"];
		$newPrice = round($oldPrice * $factor, 2);
		$inventory[$itemName]["price"] = $newPrice;

		printf("%-15s Old: $%-8.2f New: $%-8.2f\n", $itemName, $oldPrice, $newPrice);
		$index++;
	} while ($index < count($keys));

	return true;
}

#
# Optional extension: print top best-selling items from the sales log.
#
function generateBestSellingReport(array $salesLog, int $topN = 3): void
{
	echo "\n=== Best-Selling Items Report ===\n";

	if ($topN <= 0) {
		echo "Error: topN must be greater than zero.\n";
		return;
	}

	if (empty($salesLog)) {
		echo "No sales data available yet.\n";
		return;
	}

	arsort($salesLog);
	$items = array_keys($salesLog);
	$limit = min($topN, count($items));

	for ($i = 0; $i < $limit; $i++) {
		$itemName = $items[$i];
		$units = (int) $salesLog[$itemName];
		echo ($i + 1) . ". {$itemName} - {$units} units sold\n";
	}
}

#
# Validate decoded inventory data shape from JSON.
#
function isValidInventoryStructure(array $data): bool
{
	if (empty($data)) {
		return false;
	}

	foreach ($data as $itemName => $details) {
		if (!is_string($itemName) || !is_array($details)) {
			return false;
		}

		if (!array_key_exists("quantity", $details) || !array_key_exists("price", $details)) {
			return false;
		}

		if (!is_numeric($details["quantity"]) || !is_numeric($details["price"])) {
			return false;
		}

		if ((int) $details["quantity"] < 0 || (float) $details["price"] < 0) {
			return false;
		}
	}

	return true;
}

#
# Optional feature: save inventory as JSON.
#
function saveInventoryToFile(array $inventory, string $filePath): bool
{
	$json = json_encode($inventory, JSON_PRETTY_PRINT);

	if ($json === false) {
		echo "Error: Failed to encode inventory data.\n";
		return false;
	}

	$bytes = @file_put_contents($filePath, $json);
	if ($bytes === false) {
		echo "Error: Could not write to '{$filePath}'.\n";
		return false;
	}

	echo "Inventory saved to '{$filePath}'.\n";
	return true;
}

#
# Optional feature: load inventory from JSON.
#
function loadInventoryFromFile(string $filePath): ?array
{
	if (!file_exists($filePath)) {
		echo "Warning: '{$filePath}' does not exist.\n";
		return null;
	}

	$contents = @file_get_contents($filePath);
	if ($contents === false) {
		echo "Error: Could not read '{$filePath}'.\n";
		return null;
	}

	$decoded = json_decode($contents, true);
	if (!is_array($decoded)) {
		echo "Error: Invalid JSON in '{$filePath}'.\n";
		return null;
	}

	if (!isValidInventoryStructure($decoded)) {
		echo "Error: JSON data does not match expected inventory format.\n";
		return null;
	}

	return $decoded;
}

#
# Read and trim one line from CLI input.
#
function promptInput(string $message): string
{
	echo $message;
	$line = fgets(STDIN);
	if ($line === false) {
		return "";
	}

	return trim($line);
}

#
# Parse user input as integer with validation.
#
function promptInt(string $message): ?int
{
	$raw = promptInput($message);
	$value = filter_var($raw, FILTER_VALIDATE_INT);
	if ($value === false) {
		echo "Error: Please enter a valid integer.\n";
		return null;
	}

	return (int) $value;
}

#
# Parse user input as float with validation.
#
function promptFloat(string $message): ?float
{
	$raw = promptInput($message);
	if (!is_numeric($raw)) {
		echo "Error: Please enter a valid number.\n";
		return null;
	}

	return (float) $raw;
}

#
# Run non-interactive demo workflow for quick testing.
#
function runSampleWorkflow(array &$inventory, array &$salesLog, string $savePath): void
{
	displayInventory($inventory, "Initial Inventory");

	echo "\n--- Sample Quantity Updates ---\n";
	updateItemQuantity($inventory, "mouse", 28); # Case-insensitive update.
	updateItemQuantity($inventory, "Headset", 2); # Intentionally low-stock example.
	updateItemQuantity($inventory, "Laptop", 9); # Normal happy-path update.
	updateItemQuantity($inventory, "Webcam", 10); # Intentional not-found example.

	echo "\n--- Sample Sales Activity ---\n";
	recordSale($inventory, $salesLog, "Laptop", 2); # Valid sale.
	recordSale($inventory, $salesLog, "keyboard", 5); # Valid sale with mixed case.
	recordSale($inventory, $salesLog, "Headset", 1); # Another valid sale.

	lowStockAlert($inventory, 5);

	applyDiscount($inventory, 10);

	displayInventory($inventory, "Final Updated Inventory");
	generateBestSellingReport($salesLog, 3);

	if (saveInventoryToFile($inventory, $savePath)) {
		$loadedInventory = loadInventoryFromFile($savePath);
		if (is_array($loadedInventory)) {
			displayInventory($loadedInventory, "Reloaded Inventory from File");
		}
	}
}

#
# Browser-only test hook so users can manually trigger additional scenarios.
#
function runWebTestAction(array &$inventory, array &$salesLog): void
{
	if (!isWebRequest()) {
		return;
	}

	$operation = trim((string) ($_GET["test_op"] ?? ""));
	if ($operation === "") {
		return;
	}

	$item = trim((string) ($_GET["item"] ?? ""));
	$valueRaw = trim((string) ($_GET["value"] ?? ""));

	echo "\n--- Web Test Action ---\n";
	echo "Operation: {$operation}\n";

	switch ($operation) {
		case "update":
			if (!is_numeric($valueRaw)) {
				echo "Error: Value must be an integer for update operation.\n";
				return;
			}
			updateItemQuantity($inventory, $item, (int) $valueRaw);
			break;

		case "sale":
			if (!is_numeric($valueRaw)) {
				echo "Error: Value must be an integer for sale operation.\n";
				return;
			}
			recordSale($inventory, $salesLog, $item, (int) $valueRaw);
			break;

		case "discount":
			if (!is_numeric($valueRaw)) {
				echo "Error: Value must be numeric for discount operation.\n";
				return;
			}
			applyDiscount($inventory, (float) $valueRaw);
			break;

		case "lowstock":
			if (!is_numeric($valueRaw)) {
				echo "Error: Value must be an integer for lowstock operation.\n";
				return;
			}
			lowStockAlert($inventory, (int) $valueRaw);
			break;

		default:
			echo "Error: Unknown operation '{$operation}'.\n";
	}
}

#
# Run interactive command-line menu.
#
function runInteractiveMenu(array &$inventory, array &$salesLog, string $savePath): void
{
	echo "\n=== Inventory Management CLI ===\n";
	echo "Type a menu number and press Enter.\n";

	while (true) {
		echo "\nMenu:\n";
		echo "1) Display inventory\n";
		echo "2) Update item quantity\n";
		echo "3) Record sale\n";
		echo "4) Low stock alert\n";
		echo "5) Apply discount\n";
		echo "6) Best-selling report\n";
		echo "7) Save inventory\n";
		echo "8) Load inventory\n";
		echo "9) Run sample demo flow\n";
		echo "0) Exit\n";

		$choice = promptInput("Choose an option: ");

		switch ($choice) {
			case "1":
				displayInventory($inventory, "Current Inventory");
				break;

			case "2":
				$item = promptInput("Item name: ");
				$qty = promptInt("New quantity: ");
				if ($qty !== null) {
					updateItemQuantity($inventory, $item, $qty);
				}
				break;

			case "3":
				$item = promptInput("Item name sold: ");
				$units = promptInt("Units sold: ");
				if ($units !== null) {
					recordSale($inventory, $salesLog, $item, $units);
				}
				break;

			case "4":
				$threshold = promptInt("Low-stock threshold: ");
				if ($threshold !== null) {
					lowStockAlert($inventory, $threshold);
				}
				break;

			case "5":
				$discount = promptFloat("Discount percent (0-100): ");
				if ($discount !== null) {
					applyDiscount($inventory, $discount);
				}
				break;

			case "6":
				$topN = promptInt("Show top how many items? ");
				if ($topN !== null) {
					generateBestSellingReport($salesLog, $topN);
				}
				break;

			case "7":
				saveInventoryToFile($inventory, $savePath);
				break;

			case "8":
				$loaded = loadInventoryFromFile($savePath);
				if (is_array($loaded)) {
					$inventory = $loaded;
					echo "Inventory loaded into current session.\n";
				}
				break;

			case "9":
				runSampleWorkflow($inventory, $salesLog, $savePath);
				break;

			case "0":
				echo "Exiting Inventory Management CLI.\n";
				return;

			default:
				echo "Invalid option. Please choose 0-9.\n";
		}
	}
}

# 7) Main program flow
beginWebOutput();

$savePath = __DIR__ . "/inventory_data.json";
$args = $_SERVER["argv"] ?? [];
$forceInteractive = in_array("--interactive", $args, true);
$isCliExecution = PHP_SAPI === "cli" && defined("STDIN");

# Default behavior is non-interactive so requirements run end-to-end automatically.
# Use: php inventory_management.php --interactive
if ($forceInteractive && $isCliExecution) {
	runInteractiveMenu($inventory, $salesLog, $savePath);
} else {
	runSampleWorkflow($inventory, $salesLog, $savePath);
	runWebTestAction($inventory, $salesLog);
}

endWebOutput();
