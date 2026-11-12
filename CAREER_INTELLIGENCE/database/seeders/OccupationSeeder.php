<?php

namespace Database\Seeders;

use App\Models\Occupation;
use App\Models\OccupationSkill;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            // =========================================================================
            // BIDANG TEKNOLOGI INFORMASI (IT & SOFTWARE) - 12 PROFESI
            // =========================================================================
            [
                'title' => 'Full Stack Web Developer',
                'field' => 'it',
                'category' => 'Software Engineering',
                'description' => 'Merancang dan mengembangkan aplikasi web modern dari antarmuka pengguna responsif hingga arsitektur backend, basis data, dan API yang skalabel.',
                'average_salary' => 'Rp 8.000.000 - Rp 16.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Laravel & RESTful API Architecture', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Modern JavaScript (React.js / Vue.js)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Database Design (MySQL / PostgreSQL)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Git Version Control & Deployment', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'Software Testing & Clean Architecture', 'benchmark_score' => 75, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Frontend Web & Mobile Engineer',
                'field' => 'it',
                'category' => 'Frontend Development',
                'description' => 'Membangun pengalaman antarmuka web interaktif berkinerja tinggi, ramah SEO, dan adaptif untuk perangkat desktop maupun seluler.',
                'average_salary' => 'Rp 7.500.000 - Rp 15.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'React.js & Next.js Framework', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Tailwind CSS & Responsive Layouts', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'TypeScript & Modern JavaScript ES6+', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'State Management & Web Performance', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'Cross-Browser Testing & PWA', 'benchmark_score' => 75, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Backend & API Systems Engineer',
                'field' => 'it',
                'category' => 'Backend Engineering',
                'description' => 'Fokus pada keandalan logika bisnis server, pemrosesan transaksi data berskala besar, caching, dan arsitektur microservices berkecepatan tinggi.',
                'average_salary' => 'Rp 8.500.000 - Rp 17.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Microservices Architecture & REST/gRPC', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Advanced SQL & Query Optimization', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Message Queues (Redis / RabbitMQ)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Security & Authentication (OAuth/JWT)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Unit & Integration Testing (PHPUnit/Pest)', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Mobile Application Developer (Flutter & Android)',
                'field' => 'it',
                'category' => 'Mobile Development',
                'description' => 'Mengembangkan aplikasi seluler native dan cross-platform yang mulus, stabil, dan terintegrasi dengan berbagai layanan cloud API.',
                'average_salary' => 'Rp 8.000.000 - Rp 16.000.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Flutter & Dart Framework', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Android SDK & Kotlin Fundamentals', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'REST API Consumption & Offline Sync', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'State Management (Bloc / Riverpod)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Google Play Store Release & CI/CD', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Data Analyst & Business Intelligence',
                'field' => 'it',
                'category' => 'Data & Analytics',
                'description' => 'Mengolah big data, membangun model analitik, dan menyajikan visualisasi dashboard interaktif untuk mendukung keputusan strategis bisnis.',
                'average_salary' => 'Rp 8.000.000 - Rp 15.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Advanced SQL & Data Modeling', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Python Data Analysis (Pandas, NumPy)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Data Visualization (Power BI / Tableau)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Statistical Analysis & A/B Testing', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'Data Cleaning & Preprocessing Pipelines', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Data Engineer & Big Data Specialist',
                'field' => 'it',
                'category' => 'Data Engineering',
                'description' => 'Merancang arsitektur pipeline data terdistribusi (ETL), data warehouse, dan streaming data real-time untuk kebutuhan AI & Business Intelligence.',
                'average_salary' => 'Rp 9.500.000 - Rp 19.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'ETL Pipeline Design (Airflow / DBT)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Data Warehousing (BigQuery / Snowflake)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Distributed Processing (Apache Spark)', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Event Streaming (Apache Kafka)', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'Python & SQL Scripting for Big Data', 'benchmark_score' => 90, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Cloud & DevOps Infrastructure Engineer',
                'field' => 'it',
                'category' => 'Infrastructure & Cloud',
                'description' => 'Mengotomatisasi pipeline CI/CD, mengelola arsitektur cloud server, dan memastikan ketersediaan tinggi sistem 24/7.',
                'average_salary' => 'Rp 9.000.000 - Rp 18.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Linux Server Administration', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Docker & Containerization', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Kubernetes Cluster Orchestration', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'CI/CD Automation (GitHub Actions / GitLab)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Cloud Architecture (AWS / GCP / Cloudflare)', 'benchmark_score' => 82, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Cyber Security & Penetration Tester',
                'field' => 'it',
                'category' => 'Keamanan Informasi',
                'description' => 'Mengevaluasi kerentanan keamanan infrastruktur web & jaringan, melakukan penetration testing etis, dan merancang mitigasi insiden siber.',
                'average_salary' => 'Rp 9.500.000 - Rp 19.500.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Web Application Penetration Testing (OWASP)', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Network Vulnerability Scanning & Kali Linux', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Security Hardening & Firewall Rules', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Incident Response & Log Forensics (SIEM)', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'Cryptography & Secure Coding Standards', 'benchmark_score' => 82, 'is_core' => false],
                ],
            ],
            [
                'title' => 'UI/UX Designer & Product Specialist',
                'field' => 'it',
                'category' => 'Product Design',
                'description' => 'Riset kebutuhan pengguna, merancang wireframe, user flow interaktif, dan Design System standar industri dengan Figma.',
                'average_salary' => 'Rp 7.000.000 - Rp 14.000.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Figma & Advanced Prototyping', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Design System & Component Tokens', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'User Research & Usability Testing', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Information Architecture & Wireframing', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'HTML/CSS Hand-off to Frontend Developers', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Machine Learning & AI Solutions Engineer',
                'field' => 'it',
                'category' => 'Artificial Intelligence',
                'description' => 'Mengembangkan model pembelajaran mesin, pemrosesan bahasa alami (NLP), integrasi LLM (Gemini/OpenAI), dan pipeline inferensi AI.',
                'average_salary' => 'Rp 10.000.000 - Rp 22.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Python Machine Learning (Scikit-Learn, PyTorch)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Generative AI & LLM API Integration', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Natural Language Processing & Embeddings', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'AI Model Deployment via FastAPI / Docker', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Vector Databases & RAG Architecture', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Quality Assurance (QA) & Automation Tester',
                'field' => 'it',
                'category' => 'Software Quality',
                'description' => 'Menulis skenario pengujian otomatis, memverifikasi fungsionalitas end-to-end, dan memastikan keandalan produk sebelum rilis ke pengguna.',
                'average_salary' => 'Rp 7.000.000 - Rp 14.500.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Automated E2E Testing (Playwright / Cypress)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'API Automation Testing (Postman / REST Assured)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Test Planning & Bug Tracking (Jira)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Performance & Load Testing (JMeter)', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'CI/CD Pipeline Test Integration', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Network Engineer & Systems Administrator',
                'field' => 'it',
                'category' => 'Jaringan & Infrastruktur',
                'description' => 'Mengonfigurasi perangkat router/switch jaringan enterprise, merancang topologi LAN/WAN/VLAN aman, dan mengelola server virtual.',
                'average_salary' => 'Rp 7.500.000 - Rp 15.000.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Routing & Switching (Cisco / MikroTik)', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'VLAN, Subnetting & Network Topology', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Firewall Configuration & VPN Tunneling', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Server Virtualization (Proxmox / VMware)', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Network Monitoring (Zabbix / Wireshark)', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],

            // =========================================================================
            // BIDANG TEKNIK ELEKTRO & KOMPUTER (HARDWARE & IOT) - 10 PROFESI
            // =========================================================================
            [
                'title' => 'Embedded Systems & Firmware Engineer',
                'field' => 'elektro',
                'category' => 'Sistem Tertanam',
                'description' => 'Merancang perangkat keras mikrokontroler dan menulis kode firmware efisien tingkat rendah untuk sistem otomasi, otomotif, dan perangkat pintar.',
                'average_salary' => 'Rp 8.500.000 - Rp 16.500.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Pemrograman C / Embedded C++', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Arsitektur Mikrokontroler (STM32 / ESP32 / AVR)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Protokol Bus Serial (I2C, SPI, UART, CAN)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Debugging Hardware & Pengukuran Sinyal', 'benchmark_score' => 80, 'is_core' => true],
                    ['skill_name' => 'RTOS (FreeRTOS) & Manajemen Memori', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'IoT & Smart Device Solutions Specialist',
                'field' => 'elektro',
                'category' => 'Internet of Things',
                'description' => 'Mengintegrasikan sensor fisik, modul komunikasi nirkabel jarak jauh, dan platform cloud untuk monitoring telemetri dan smart instrumentation.',
                'average_salary' => 'Rp 8.000.000 - Rp 15.500.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Integrasi Sensor Fisik & Aktuator', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Protokol Komunikasi IoT (MQTT, HTTP, LoRaWAN)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Konektivitas Cloud IoT & Dashboard Telemetri', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Desain Rangkaian Elektronika & Power Management', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Edge Computing & Pemrograman Python/C', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'PLC & Industrial Automation Specialist',
                'field' => 'elektro',
                'category' => 'Otomasi Industri',
                'description' => 'Mengembangkan program kontrol mesin industri menggunakan PLC, sistem kendali terdistribusi, serta perancangan antarmuka visual HMI/SCADA.',
                'average_salary' => 'Rp 8.500.000 - Rp 17.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'Pemrograman PLC (Ladder Diagram / FBD)', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Desain HMI & Integrasi SCADA', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Instrumentasi & Kontrol Umpan Balik (PID)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Standar Keselamatan Kelistrikan Industri', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Troubleshooting Motor Listrik & Inverter VFD', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Hardware & PCB Design Engineer',
                'field' => 'elektro',
                'category' => 'Desain Perangkat Keras',
                'description' => 'Merancang skematik sirkuit elektronika, tata letak multi-layer PCB dengan Altium/KiCad, routing sinyal frekuensi tinggi, dan verifikasi prototipe.',
                'average_salary' => 'Rp 8.000.000 - Rp 16.500.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'PCB Layout & Schematic (Altium / KiCad)', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'High-Speed Routing & Signal Integrity', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Komponen Pasif/Aktif Sourcing & BOM Management', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'SMD Soldering & Hardware Prototype Assembly', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'EMC/EMI Compliance Testing', 'benchmark_score' => 78, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Robotics & Mechatronics Systems Engineer',
                'field' => 'elektro',
                'category' => 'Robotika & Mekatronika',
                'description' => 'Mengintegrasikan mekanisme mekanik, motor servo/stepper presisi tinggi, sensor vision, dan algoritma kontrol navigasi robotika otonom.',
                'average_salary' => 'Rp 9.000.000 - Rp 18.500.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Robot Operating System (ROS / ROS2)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Motor Control & Servo/Stepper Drivers', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Sensor Fusion (IMU, LiDAR, Ultrasonic)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Kinematics & Path Planning Algorithms', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Computer Vision for Robotics (OpenCV)', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Electrical Power & Renewable Energy Engineer',
                'field' => 'elektro',
                'category' => 'Tenaga Listrik & EBT',
                'description' => 'Merancang sistem pembangkit listrik tenaga surya (PLTS), sistem penyimpanan energi baterai (BESS), dan manajemen beban gardu distribusi.',
                'average_salary' => 'Rp 8.500.000 - Rp 17.500.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Perancangan PLTS & Sistem Solar PV Inverter', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Analisis Beban & Sistem Distribusi Tenaga Listrik', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Battery Energy Storage Systems (BESS)', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Simulasi Kelistrikan (ETAP / MATLAB Simulink)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Proteksi Relay & Pembumian (Grounding)', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Instrumentation & Process Control Specialist',
                'field' => 'elektro',
                'category' => 'Instrumentasi & Kontrol',
                'description' => 'Mengalibrasi transmitter instrumen cerdas (suhu, tekanan, laju aliran), kontrol katup pneumatik, dan sistem keselamatan pabrik kimia/minyak.',
                'average_salary' => 'Rp 8.500.000 - Rp 18.000.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Kalibrasi Smart Transmitter 4-20mA & HART Protocol', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Kontrol Proses Tertutup & Tuning Loop PID', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Sistem Katup Pneumatik & Aktuator Hidrolik', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Diagram P&ID (Piping and Instrumentation Diagram)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Safety Instrumented Systems (SIS / SIL)', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Telecommunication & RF Network Specialist',
                'field' => 'elektro',
                'category' => 'Telekomunikasi',
                'description' => 'Mengoptimalkan jaringan transmisi nirkabel seluler (4G/5G), penyambungan kabel fiber optik (splicing), dan instalasi microwave radio link.',
                'average_salary' => 'Rp 8.000.000 - Rp 16.000.000',
                'demand_level' => 'Menengah',
                'skills' => [
                    ['skill_name' => 'Jaringan Akses Seluler (4G LTE / 5G NR)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Fiber Optic Splicing & Pengujian OTDR', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Radio Frequency (RF) Planning & Link Budget', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Antenna Alignment & VSWR Measurement', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'IP Networking for Telecom Backbone', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Edge AI & TinyML Hardware Specialist',
                'field' => 'elektro',
                'category' => 'Edge AI & Microcontrollers',
                'description' => 'Menerapkan model deep learning terkuantisasi (INT8) ke chip mikrokontroler hemat daya untuk klasifikasi audio, getaran, dan computer vision.',
                'average_salary' => 'Rp 9.500.000 - Rp 20.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['skill_name' => 'TensorFlow Lite for Microcontrollers (TFLM)', 'benchmark_score' => 88, 'is_core' => true],
                    ['skill_name' => 'Edge Impulse Platform & Audio/Motion Dataset', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Model Quantization (Float32 to INT8)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Digital Signal Processing (FFT, Mel-Filterbank)', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Ultra-Low Power Firmware Optimization', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
            [
                'title' => 'Industrial Electrical Maintenance & Safety Engineer',
                'field' => 'elektro',
                'category' => 'Pemeliharaan Listrik & K3',
                'description' => 'Memastikan keandalan sistem kelistrikan gardu pabrik 380V, perawatan preventif transformator, proteksi panel, dan penerapan standar K3 listrik.',
                'average_salary' => 'Rp 8.000.000 - Rp 15.500.000',
                'demand_level' => 'Tinggi',
                'skills' => [
                    ['skill_name' => 'Pemeliharaan Panel Distribusi Listrik 380V', 'benchmark_score' => 90, 'is_core' => true],
                    ['skill_name' => 'Standar Keselamatan Kerja Listrik (K3 Listrik)', 'benchmark_score' => 92, 'is_core' => true],
                    ['skill_name' => 'Pengujian Termografi Inframerah & Insulation Tester', 'benchmark_score' => 85, 'is_core' => true],
                    ['skill_name' => 'Sinkronisasi Genset & Automatic Transfer Switch (ATS)', 'benchmark_score' => 82, 'is_core' => true],
                    ['skill_name' => 'Preventive & Predictive Maintenance Scheduling', 'benchmark_score' => 80, 'is_core' => false],
                ],
            ],
        ];

        foreach ($occupations as $occData) {
            $skills = $occData['skills'];
            unset($occData['skills']);

            $occupation = Occupation::updateOrCreate(
                ['title' => $occData['title']],
                $occData
            );

            foreach ($skills as $skill) {
                OccupationSkill::updateOrCreate(
                    [
                        'occupation_id' => $occupation->id,
                        'skill_name' => $skill['skill_name'],
                    ],
                    $skill
                );
            }
        }
    }
}
