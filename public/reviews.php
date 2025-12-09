<?php
require_once __DIR__ . '/../templates/header.php';


// CEK LOGIN DAN LOAD MODEL
$auth = new Auth($db->pdo());
$isLogin = $auth->check();

$bookModel   = new BookModel($db->pdo());
$reviewModel = new ReviewModel($db->pdo());

/*
 AMBIL ID BUKU DARI URL
 Contoh: reviews.php?book_id=5
*/
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;


// AMBIL DATA BUKU DARI DATABASE
$book = $bookModel->find($book_id);

// Jika buku tidak ditemukan maka tampilkan pesan 
if (!$book) {
    echo "<div class='max-w-3xl mx-auto py-10 text-center text-red-400'>
            Buku tidak ditemukan.
          </div>";
    require_once __DIR__ . '/../templates/footer.php';
    exit;
}

// AMBIL SEMUA ULASAN UNTUK BUKU INI
$reviews = $reviewModel->getAllReviewsByBook($book_id);

// AMBIL RATING RATA-RATA DAN JUMLAH ULASAN
$ratingData  = $reviewModel->getBookRating($book_id);
$avgRating   = $ratingData['avg_rating'] ?? 0;
$reviewCount = $ratingData['count_review'] ?? 0;
?>

<style>

    .card-shinigami {
        background: linear-gradient(160deg, #0c0c0d, #111113 55%, #0c0c0d);
        border: 1px solid rgba(168,85,247,0.18);
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 0 20px rgba(168,85,247,0.12);
        transition: .25s ease;
    }

    .card-shinigami:hover {
        box-shadow: 0 0 30px rgba(168,85,247,0.22);
        border-color: rgba(168,85,247,0.28);
    }

    .title-shinigami {
        font-size: 1.8rem;
        font-weight: 800;
        color: #d8b4fe;
        text-shadow: 0 0 16px rgba(168,85,247,0.45);
    }

    .review-box {
        background: #1b1b1e;
        border: 1px solid #3a0d5e;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 0 10px rgba(168,85,247,0.18);
        transition: .20s;
    }

    .review-box:hover {
        box-shadow: 0 0 14px rgba(168,85,247,0.28);
        border-color: rgba(168,85,247,0.4);
    }

    .text-muted {
        color: #b3b3b3;
    }

    .back-link {
        color: #a855f7;
        text-shadow: 0 0 8px rgba(168,85,247,0.45);
    }

    .back-link:hover {
        color: #c084fc;
        text-shadow: 0 0 12px rgba(168,85,247,0.6);
    }
</style>


<div class="max-w-3xl mx-auto py-10">

    <!-- LINK KEMBALI -->
    <a href="index.php" class="back-link">&larr; Kembali ke daftar buku</a>

    <div class="card-shinigami mt-5">

        <!-- HEADER INFO BUKU -->
        <div class="flex gap-5">

            <!-- COVER BUKU -->
            <div class="h-40 w-32 rounded overflow-hidden bg-gray-700 flex items-center justify-center">
                <?php if (!empty($book['cover'])): ?>
                    <img src="uploads/cover/<?= htmlspecialchars($book['cover']) ?>" 
                         class="h-full w-full object-cover">
                <?php else: ?>
                    <span class="text-gray-400 text-sm">No Cover</span>
                <?php endif; ?>
            </div>

            <!-- DETAIL BUKU -->
            <div>
                <h1 class="title-shinigami">
                    <?= htmlspecialchars($book['judul']) ?>
                </h1>

                <p class="text-muted text-sm mb-2">
                    Oleh: <?= htmlspecialchars($book['penulis']) ?>
                </p>

                <!-- RATING RATA-RATA -->
                <div class="flex items-center gap-1">
                    <?php
                    // Render bintang sesuai rating rata-rata
                    for ($i = 1; $i <= 5; $i++):
                        if ($avgRating >= $i) {
                            echo '<span class="text-yellow-400 text-xl">★</span>';
                        } elseif ($avgRating >= $i - 0.5) {
                            echo '<span class="text-yellow-400 text-xl">☆</span>';
                        } else {
                            echo '<span class="text-gray-600 text-xl">★</span>';
                        }
                    endfor;
                    ?>

                    <span class="ml-1 text-sm text-gray-300">
                        (<?= number_format($avgRating, 1) ?>/5)
                    </span>

                    <?php if ($reviewCount > 0): ?>
                        <span class="text-xs text-gray-400">• <?= $reviewCount ?> ulasan</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- DAFTAR ULASAN -->
        <h2 class="text-lg font-semibold mt-8 mb-4 text-purple-300">
            Ulasan Pembaca
        </h2>

        <!-- Jika belum ada ulasan -->
        <?php if (empty($reviews)): ?>

            <p class="text-gray-400 italic">Belum ada ulasan untuk buku ini.</p>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($reviews as $r): ?>
                    <div class="review-box">

                        <!-- USERNAME + RATING -->
                        <div class="flex items-center justify-between">

                            <div class="font-bold text-purple-200">
                                <?= htmlspecialchars($r['username']) ?>
                            </div>

                            <div class="flex items-center">
                                <?php
                                // Render rating setiap user
                                for ($i = 1; $i <= 5; $i++):
                                    if ($r['rating'] >= $i) {
                                        echo '<span class="text-yellow-400 text-lg">★</span>';
                                    } else {
                                        echo '<span class="text-gray-600 text-lg">★</span>';
                                    }
                                endfor;
                                ?>
                                <span class="text-sm ml-1 text-gray-300">
                                    (<?= $r['rating'] ?>/5)
                                </span>
                            </div>

                        </div>

                        <!-- KOMENTAR ULASAN -->
                        <p class="text-gray-300 mt-2">
                            <?= nl2br(htmlspecialchars($r['komentar'])) ?>
                        </p>

                        <!-- TANGGAL DIBUAT -->
                        <p class="text-xs text-gray-500 mt-1">
                            Ditulis pada: <?= $r['created_at'] ?>
                        </p>

                    </div>
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
