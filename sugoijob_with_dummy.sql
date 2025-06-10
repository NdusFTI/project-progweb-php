-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 01:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sugoijob`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_banner` varchar(255) DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `company_name`, `company_logo`, `company_banner`, `company_description`, `city`, `full_address`, `website`, `created_at`) VALUES
(1, 3, 'PT Tokopedia', 'https://lf-web-assets.tokopedia-static.net/obj/tokopedia-web-sg/arael_v3/0c292173.png', 'https://lelogama.go-jek.com/post_featured_image/Super-App-Gojek-Banner.jpg', 'Tokopedia adalah perusahaan teknologi Indonesia yang bergerak di bidang perdagangan elektronik. Kami berkomitmen untuk memajukan ekosistem digital Indonesia dan memberdayakan UMKM di seluruh Indonesia.', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'https://www.tokopedia.com', '2025-06-03 10:16:18'),
(2, 4, 'PT Gojek Indonesia', 'https://lelogama.go-jek.com/cms_editor/2021/05/28/info-gojek-2.png', 'https://lelogama.go-jek.com/post_featured_image/gakpakelama-header.png', 'Gojek adalah perusahaan teknologi asal Indonesia yang melayani jutaan pengguna di Asia Tenggara. Kami menyediakan berbagai layanan on-demand seperti transportasi, pengiriman makanan, pembayaran digital, dan berbagai layanan gaya hidup lainnya.', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'https://www.gojek.com', '2025-06-03 10:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `cv_file` varchar(255) NOT NULL,
  `portfolio_file` varchar(255) DEFAULT NULL,
  `cover_letter_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','interview','accepted','rejected') DEFAULT 'pending',
  `company_notes` text DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE `job_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_categories`
--

INSERT INTO `job_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Information Technology', '2025-06-03 10:16:18'),
(2, 'Marketing & Communications', '2025-06-03 10:16:18'),
(3, 'Operations & Logistics', '2025-06-03 10:16:18'),
(4, 'Finance & Accounting', '2025-06-03 10:16:18'),
(5, 'Human Resources', '2025-06-03 10:16:18'),
(6, 'Product Management', '2025-06-03 10:16:18'),
(7, 'Data Science & Analytics', '2025-06-03 10:16:18'),
(8, 'Customer Service', '2025-06-03 10:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `salary_min` int(11) DEFAULT NULL,
  `salary_max` int(11) DEFAULT NULL,
  `salary_text` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `job_type` set('Full-Time','Part-Time','Kontrak','Internship') NOT NULL,
  `experience_required` varchar(100) DEFAULT NULL,
  `application_deadline` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `views_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`id`, `company_id`, `category_id`, `title`, `description`, `requirements`, `salary_min`, `salary_max`, `salary_text`, `location`, `full_address`, `job_type`, `experience_required`, `application_deadline`, `is_active`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Senior Software Engineer - Backend', 'Kami mencari Senior Software Engineer yang berpengalaman untuk bergabung dengan tim Backend Development. Anda akan bertanggung jawab mengembangkan dan memelihara sistem backend yang mendukung platform e-commerce terbesar di Indonesia.', '• Minimal S1 Teknik Informatika/Ilmu Komputer\r\n• Pengalaman minimal 4 tahun sebagai Backend Developer\r\n• Menguasai bahasa pemrograman Go, Java, atau Python\r\n• Berpengalaman dengan microservices architecture\r\n• Familiar dengan database PostgreSQL, Redis, dan MongoDB\r\n• Memahami cloud computing (AWS/GCP)\r\n• Memiliki pengalaman dengan Docker dan Kubernetes', 12000000, 18000000, 'Rp 12.000.000 - Rp 18.000.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'Backend Development: 4 tahun', '2025-07-15', 1, 245, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(2, 1, 6, 'Product Manager - Mobile App', 'Bergabunglah dengan tim Product Management kami untuk mengembangkan fitur-fitur inovatif di aplikasi mobile Tokopedia. Anda akan bekerja sama dengan tim Engineering, Design, dan Business untuk menciptakan pengalaman pengguna yang luar biasa.', '• Minimal S1 semua jurusan, preferensi Teknik Industri/Manajemen\r\n• Pengalaman minimal 3 tahun sebagai Product Manager\r\n• Memahami user experience dan product development lifecycle\r\n• Familiar dengan tools seperti Figma, Jira, dan analytics tools\r\n• Kemampuan analisis data yang kuat\r\n• Pengalaman di industri e-commerce atau teknologi\r\n• Kemampuan komunikasi dan leadership yang baik', 15000000, 22000000, 'Rp 15.000.000 - Rp 22.000.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'Product Management: 3 tahun', '2025-07-20', 1, 189, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(3, 1, 2, 'Digital Marketing Specialist', 'Kami mencari Digital Marketing Specialist untuk mengembangkan strategi marketing digital dan meningkatkan brand awareness Tokopedia. Anda akan mengelola campaign digital dan menganalisis performa marketing.', '• Minimal S1 Marketing, Komunikasi, atau bidang terkait\r\n• Pengalaman minimal 2 tahun di digital marketing\r\n• Menguasai Google Ads, Facebook Ads, dan platform advertising lainnya\r\n• Familiar dengan Google Analytics dan tools marketing automation\r\n• Memahami SEO/SEM dan content marketing\r\n• Kemampuan analisis data marketing yang baik\r\n• Kreatif dan up-to-date dengan tren digital marketing', 8000000, 12000000, 'Rp 8.000.000 - Rp 12.000.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'Digital Marketing: 2 tahun', '2025-07-18', 1, 167, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(4, 1, 5, 'HR Business Partner', 'Bergabunglah dengan tim HR kami sebagai Business Partner untuk mendukung berbagai inisiatif strategis perusahaan. Anda akan bekerja sama dengan leadership untuk pengembangan talent dan organizational development.', '• Minimal S1 Psikologi, Manajemen SDM, atau bidang terkait\r\n• Pengalaman minimal 3 tahun sebagai HR Business Partner atau HR Generalist\r\n• Memahami talent acquisition, performance management, dan employee engagement\r\n• Familiar dengan HRIS dan tools HR analytics\r\n• Kemampuan komunikasi dan interpersonal yang excellent\r\n• Memiliki pemahaman tentang employment law\r\n• Pengalaman di perusahaan teknologi menjadi nilai plus', 10000000, 15000000, 'Rp 10.000.000 - Rp 15.000.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'HR Business Partner: 3 tahun', '2025-07-12', 1, 134, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(5, 1, 1, 'Frontend Developer - React', 'Kami mencari Frontend Developer yang ahli dalam React untuk mengembangkan user interface yang responsif dan user-friendly. Anda akan berkontribusi dalam menciptakan pengalaman belanja online terbaik untuk jutaan pengguna.', '• Minimal S1 Teknik Informatika/Ilmu Komputer\r\n• Pengalaman minimal 2 tahun dalam Frontend development\r\n• Menguasai React.js, JavaScript ES6+, HTML5, CSS3\r\n• Familiar dengan Redux, React Hooks, dan modern frontend tools\r\n• Memahami responsive design dan cross-browser compatibility\r\n• Pengalaman dengan version control Git\r\n• Familiar dengan testing frameworks (Jest, React Testing Library)\r\n• Memahami RESTful APIs dan integration', 9000000, 14000000, 'Rp 9.000.000 - Rp 14.000.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'Frontend Development: 2 tahun', '2025-07-22', 1, 198, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(6, 1, 4, 'Financial Analyst', 'Bergabunglah dengan tim Finance kami untuk melakukan analisis keuangan dan mendukung strategic planning perusahaan. Anda akan terlibat dalam budgeting, forecasting, dan business intelligence.', '• Minimal S1 Akuntansi, Keuangan, atau Ekonomi\r\n• Pengalaman minimal 2 tahun sebagai Financial Analyst\r\n• Menguasai Excel advanced dan tools analisis keuangan\r\n• Familiar dengan SQL dan business intelligence tools\r\n• Memahami financial modeling dan valuation\r\n• Kemampuan analisis yang kuat dan detail-oriented\r\n• Pengalaman di industri teknologi atau e-commerce preferred', 9500000, 14500000, 'Rp 9.500.000 - Rp 14.500.000 per bulan', 'Jakarta', 'Jl. M.H. Thamrin No.81, Gondangdia, Menteng, Jakarta Pusat 10310', 'Full-Time', 'Financial Analysis: 2 tahun', '2025-07-28', 1, 112, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(7, 2, 1, 'Mobile Developer - Android', 'Kami mencari Android Developer yang passionate untuk mengembangkan aplikasi mobile yang digunakan oleh jutaan pengguna. Anda akan bekerja dalam tim yang dinamis untuk menciptakan solusi teknologi yang berdampak besar.', '• Minimal S1 Teknik Informatika/Ilmu Komputer\r\n• Pengalaman minimal 2 tahun dalam Android development\r\n• Menguasai Kotlin dan Java\r\n• Familiar dengan Android SDK, Android Studio\r\n• Memahami RESTful APIs dan JSON\r\n• Pengalaman dengan Git version control\r\n• Memahami design patterns (MVP, MVVM)\r\n• Familiar dengan testing frameworks', 10000000, 16000000, 'Rp 10.000.000 - Rp 16.000.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'Android Development: 2 tahun', '2025-07-10', 1, 156, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(8, 2, 7, 'Data Scientist', 'Bergabunglah dengan tim Data Science Gojek untuk menganalisis data dan mengembangkan machine learning models yang mendukung berbagai layanan kami. Anda akan bekerja dengan big data untuk menciptakan insights yang valuable.', '• Minimal S1 Matematika, Statistika, atau Teknik Informatika\r\n• Pengalaman minimal 2 tahun sebagai Data Scientist\r\n• Menguasai Python dan R\r\n• Familiar dengan machine learning libraries (scikit-learn, TensorFlow, PyTorch)\r\n• Pengalaman dengan SQL dan big data tools (Spark, Hadoop)\r\n• Memahami statistical analysis dan data visualization\r\n• Familiar dengan cloud platforms (AWS, GCP)\r\n• Kemampuan komunikasi yang baik untuk mempresentasikan findings', 11000000, 17000000, 'Rp 11.000.000 - Rp 17.000.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'Data Science: 2 tahun', '2025-07-25', 1, 203, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(9, 2, 1, 'iOS Developer', 'Kami mencari iOS Developer untuk mengembangkan aplikasi iOS Gojek yang inovatif. Anda akan bertanggung jawab membangun fitur-fitur baru dan meningkatkan performa aplikasi untuk memberikan pengalaman terbaik kepada pengguna.', '• Minimal S1 Teknik Informatika/Ilmu Komputer\r\n• Pengalaman minimal 2 tahun dalam iOS development\r\n• Menguasai Swift dan Objective-C\r\n• Familiar dengan Xcode, iOS SDK, dan Apple development tools\r\n• Memahami iOS design patterns dan best practices\r\n• Pengalaman dengan Core Data, RESTful web services\r\n• Familiar dengan version control Git\r\n• Memahami App Store submission process', 10500000, 16500000, 'Rp 10.500.000 - Rp 16.500.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'iOS Development: 2 tahun', '2025-07-14', 1, 142, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(10, 2, 3, 'Operations Manager - Logistics', 'Bergabunglah dengan tim Operations untuk mengelola dan mengoptimalkan operasional logistik GoSend dan layanan delivery lainnya. Anda akan bertanggung jawab meningkatkan efisiensi operasional dan customer satisfaction.', '• Minimal S1 Teknik Industri, Manajemen Operasi, atau bidang terkait\r\n• Pengalaman minimal 3 tahun di operations management\r\n• Memahami supply chain management dan logistics\r\n• Familiar dengan data analysis dan process improvement\r\n• Kemampuan leadership dan team management\r\n• Pengalaman di industri logistics atau on-demand services\r\n• Memiliki analytical thinking dan problem-solving skills', 12000000, 18000000, 'Rp 12.000.000 - Rp 18.000.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'Operations Management: 3 tahun', '2025-07-16', 1, 178, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(11, 2, 6, 'Senior Product Manager - Payments', 'Kami mencari Senior Product Manager untuk memimpin pengembangan produk GoPay dan solusi payment lainnya. Anda akan bekerja dengan cross-functional teams untuk menciptakan inovasi di bidang fintech.', '• Minimal S1 semua jurusan, preferensi Teknik/Bisnis\r\n• Pengalaman minimal 4 tahun sebagai Product Manager\r\n• Memahami fintech dan digital payments landscape\r\n• Familiar dengan product development lifecycle dan agile methodology\r\n• Kemampuan analytical thinking dan data-driven decision making\r\n• Pengalaman dengan A/B testing dan user research\r\n• Leadership skills dan kemampuan stakeholder management\r\n• Pengalaman di fintech atau payments industry preferred', 18000000, 25000000, 'Rp 18.000.000 - Rp 25.000.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'Product Management: 4 tahun', '2025-07-30', 1, 267, '2025-06-03 10:16:18', '2025-06-03 10:16:18'),
(12, 2, 8, 'Customer Experience Specialist', 'Bergabunglah dengan tim Customer Experience untuk memastikan kepuasan pengguna Gojek. Anda akan menangani customer inquiries, menganalisis feedback, dan berkontribusi dalam peningkatan customer journey.', '• Minimal S1 semua jurusan\r\n• Pengalaman minimal 1 tahun di customer service atau related field\r\n• Kemampuan komunikasi yang excellent (verbal dan written)\r\n• Patient, empathetic, dan customer-oriented\r\n• Familiar dengan CRM tools dan ticketing systems\r\n• Kemampuan multitasking dan bekerja dalam lingkungan fast-paced\r\n• Fluent dalam Bahasa Indonesia dan Bahasa Inggris\r\n• Pengalaman di tech company atau startup preferred', 6000000, 9000000, 'Rp 6.000.000 - Rp 9.000.000 per bulan', 'Jakarta', 'Jl. Iskandarsyah II No.2, Melawai, Kebayoran Baru, Jakarta Selatan 12160', 'Full-Time', 'Customer Service: 1 tahun', '2025-07-08', 1, 98, '2025-06-03 10:16:18', '2025-06-03 10:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('job_seeker','company') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `created_at`) VALUES
(1, 'ndus@gmail.com', '123', 'Bernadus', 'job_seeker', '2025-06-03 10:16:18'),
(2, 'ivan@gmail.com', '123', 'Ivan', 'job_seeker', '2025-06-03 10:16:18'),
(3, 'hr@tokopedia.com', 'tokopedia', 'PT Tokopedia', 'company', '2025-06-03 10:16:18'),
(4, 'hr@gojek.com', 'gojek', 'PT Gojek Indonesia', 'company', '2025-06-03 10:16:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`job_id`,`applicant_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_categories`
--
ALTER TABLE `job_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job_postings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_postings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `job_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
