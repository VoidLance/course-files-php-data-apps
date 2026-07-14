<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

# =============================================================================
# BOOK CLASS
# Represents a single book with title, author, ISBN, and availability status.
# =============================================================================

class Book {
    public string $title;
    public string $author;
    public string $isbn;
    public bool $isAvailable;

    public function __construct(string $title, string $author, string $isbn, bool $isAvailable = true) {
        $this->title       = $title;
        $this->author      = $author;
        $this->isbn        = $isbn;
        $this->isAvailable = $isAvailable;
    }
}

# =============================================================================
# LIBRARY CLASS
# Manages a collection of Book objects. Supports adding, removing, searching,
# borrowing, and returning books.
# =============================================================================

class Library {
    # @var Book[] Library shelf inventory.
    private array $books = [];

    # Add a book to the library collection.
    public function addBook(Book $book): void {
        # One ISBN, one book. No clone army.
        if ($this->findBookByIsbn($book->isbn) !== null) {
            echo "  [ERROR] A book with ISBN {$book->isbn} already exists in the library.\n";
            return;
        }
        $this->books[] = $book;
        echo "  [ ADD ] \"{$book->title}\" by {$book->author}.\n";
    }

    # Remove a book from the library by ISBN.
    public function removeBook(string $isbn): bool {
        foreach ($this->books as $index => $book) {
            if ($book->isbn === $isbn) {
                echo "  [ DEL ] \"{$book->title}\" (ISBN: {$isbn}).\n";
                unset($this->books[$index]);
				$this->books = array_values($this->books); # Re-index after delete.
                return true;
            }
        }
        echo "  [ERROR] No book with ISBN {$isbn} found.\n";
        return false;
    }

    # Search title/author using a case-insensitive partial match.
    public function search(string $query): array {
        $query   = strtolower($query);
        $results = [];

        foreach ($this->books as $book) {
            if (
                str_contains(strtolower($book->title), $query) ||
                str_contains(strtolower($book->author), $query)
            ) {
                $results[] = $book;
            }
        }

        return $results;
    }

    # Find one book by exact ISBN.
    public function findBookByIsbn(string $isbn): ?Book {
        foreach ($this->books as $book) {
            if ($book->isbn === $isbn) {
                return $book;
            }
        }
        return null;
    }

    # Borrow a book by ISBN and mark it unavailable.
    public function borrowBook(string $isbn): ?Book {
        $book = $this->findBookByIsbn($isbn);

        if ($book === null) {
            echo "  [ERROR] No book with ISBN {$isbn} found.\n";
            return null;
        }

        if (!$book->isAvailable) {
            echo "  [ERROR] \"{$book->title}\" is already borrowed.\n";
            return null;
        }

        # Objects are reference-like here, so this updates the same shelf copy.
        markBookUnavailable($book);
        echo "  [ OUT ] \"{$book->title}\".\n";
        return $book;
    }

    # Return a borrowed book by ISBN and mark it available.
    public function returnBook(string $isbn): bool {
        $book = $this->findBookByIsbn($isbn);

        if ($book === null) {
            echo "  [ERROR] No book with ISBN {$isbn} found in the library.\n";
            return false;
        }

        if ($book->isAvailable) {
            echo "  [ERROR] \"{$book->title}\" is already available — it was not borrowed.\n";
            return false;
        }

        $book->isAvailable = true;
        echo "  [  IN ] \"{$book->title}\".\n";
        return true;
    }

    # Return all books in the collection.
    public function getBooks(): array {
        return $this->books;
    }
}

# =============================================================================
# STANDALONE HELPER FUNCTIONS
# =============================================================================

#
# Display a single book's information in a formatted table row.
#
function displayBook(Book $book): void {
    $status = $book->isAvailable ? "[  OK  ] Available" : "[BORROW] Borrowed ";
    $title  = str_pad(mb_substr($book->title,  0, 30), 30);
    $author = str_pad(mb_substr($book->author, 0, 22), 22);
    echo "| {$title} | {$author} | {$book->isbn}  | {$status} |\n";
}

#
# Print the column header row and separator for the books table.
#
function printTableHeader(): void {
    printTableSeparator();
    echo "| " . str_pad("Title",  30) . " | " . str_pad("Author", 22) . " | ISBN              | Status             |\n";
    printTableSeparator();
}

# Print a horizontal separator line for the books table.
function printTableSeparator(): void {
    echo "+" . str_repeat("-", 32) . "+" . str_repeat("-", 24) . "+" . str_repeat("-", 19) . "+" . str_repeat("-", 20) . "+\n";
}

#
# Display all books in the library as a formatted table.
# Accepts the Library instance; objects are passed by reference in PHP, so
# this function reads the live state without needing an explicit &.
#
function displayAllBooks(Library $library): void {
    $books = $library->getBooks();

    if (empty($books)) {
        echo "  (No books in the library.)\n";
        return;
    }

    printTableHeader();
    foreach ($books as $book) {
        displayBook($book);
    }
    printTableSeparator();
    $count = count($books);
    echo "  {$count} book(s) total.\n";
}

#
# Mark a book as unavailable (borrowed).
# Demonstrates passing by reference — PHP objects are reference types, so
# modifying $book here changes the original instance stored in the Library.
#
function markBookUnavailable(Book &$book): void {
    $book->isAvailable = false;
}

# =============================================================================
# DEMONSTRATION
# =============================================================================

$width = 62;
echo str_repeat("=", $width) . "\n";
echo str_pad(" LIBRARY MANAGEMENT SYSTEM", $width) . "\n";
echo str_repeat("=", $width) . "\n\n";

# --- Create library and add books ---
$library = new Library();

echo "\n>>> Adding books\n";
echo str_repeat("-", 40) . "\n";
$library->addBook(new Book("The Pragmatic Programmer", "David Thomas",      "978-0135957059"));
$library->addBook(new Book("Clean Code",               "Robert C. Martin", "978-0132350884"));
$library->addBook(new Book("The Hobbit",               "J.R.R. Tolkien",   "978-0547928227"));
$library->addBook(new Book("1984",                     "George Orwell",    "978-0451524935"));
$library->addBook(new Book("Dune",                     "Frank Herbert",    "978-0441013593"));

# Attempt to add a duplicate ISBN — error handling
$library->addBook(new Book("Duplicate Book", "Some Author", "978-0132350884"));

echo "\n>>> All books in the library\n";
displayAllBooks($library);

# --- Search ---
echo "\n>>> Search: 'orwell'\n";
$results = $library->search("orwell");
if (!empty($results)) {
    printTableHeader();
    foreach ($results as $found) {
        displayBook($found);
    }
    printTableSeparator();
} else {
    echo "  No results found.\n";
}

echo "\n>>> Search: 'code' (partial title match)\n";
$results = $library->search("code");
if (!empty($results)) {
    printTableHeader();
    foreach ($results as $found) {
        displayBook($found);
    }
    printTableSeparator();
}

echo "\n>>> Search: 'nonexistent'\n";
$results = $library->search("nonexistent");
if (empty($results)) {
    echo "  No results found.\n";
}

# --- Borrow books ---
echo "\n>>> Borrowing books\n";
echo str_repeat("-", 40) . "\n";
$library->borrowBook("978-0132350884"); # Clean Code — should succeed
$library->borrowBook("978-0132350884"); # Already borrowed — error
$library->borrowBook("978-9999999999"); # Doesn't exist — error

echo "\n>>> Library state after borrowing\n";
displayAllBooks($library);

# --- Return a book ---
echo "\n>>> Returning books\n";
echo str_repeat("-", 40) . "\n";
$library->returnBook("978-0132350884");

# Attempt to return a book that was never borrowed
$library->returnBook("978-0451524935");

echo "\n>>> Library state after return\n";
displayAllBooks($library);

# --- Remove a book ---
echo "\n>>> Removing books\n";
echo str_repeat("-", 40) . "\n";
$library->removeBook("978-0547928227");
$library->removeBook("978-0000000000"); # Non-existent ISBN — error

echo "\n>>> Final library state\n";
displayAllBooks($library);
echo "\n" . str_repeat("=", 62) . "\n";
