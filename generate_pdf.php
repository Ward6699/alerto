<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Include database connection
include('db_connection.php');

// Include DOMpdf
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Get user information
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch emergency contacts
$contacts_sql = "SELECT * FROM emergency_contacts WHERE user_id = '$user_id' ORDER BY contact_id ASC";
$contacts_result = mysqli_query($conn, $contacts_sql);
$contacts = [];
while ($row = mysqli_fetch_assoc($contacts_result)) {
    $contacts[] = $row;
}

// Fetch emergency kit items
$kit_sql = "SELECT * FROM emergency_kit WHERE user_id = '$user_id' ORDER BY category ASC, item_id ASC";
$kit_result = mysqli_query($conn, $kit_sql);
$kit_items = [];
while ($row = mysqli_fetch_assoc($kit_result)) {
    $kit_items[] = $row;
}

// Group kit items by category
$kit_by_category = [];
foreach ($kit_items as $item) {
    $kit_by_category[$item['category']][] = $item;
}

mysqli_close($conn);

// Current date
$current_date = date('F d, Y');

// Generate HTML content for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 20mm 15mm 30mm 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #dc2626;
            font-size: 28px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
            margin: 5px 0;
        }
        
        .user-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }
        
        .section-title {
            background-color: #dc2626;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background-color: #3a3a3a;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }
        
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .category-header {
            background-color: #e8e8e8;
            font-weight: bold;
            padding: 8px 10px;
            margin-top: 10px;
            font-size: 12px;
            color: #dc2626;
            text-transform: uppercase;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px 15mm;
            border-top: 2px solid #ddd;
            font-size: 10px;
            color: #888;
            background-color: white;
        }
        
        .footer p {
            margin: 3px 0;
        }
        

        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        
        .content {
            margin-bottom: 60px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="header">
            <h1>My Emergency Preparedness Report</h1>
            <p>Generated on: ' . $current_date . '</p>
        </div>
        
        <div class="user-info">
            <strong>Prepared for:</strong> ' . htmlspecialchars($name) . '
        </div>
        
        <!-- Emergency Contacts Section -->
        <div class="section-title">Emergency Contacts</div>';

if (count($contacts) > 0) {
    $html .= '
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 20%;">Phone Number</th>
                    <th style="width: 20%;">Relationship</th>
                    <th style="width: 35%;">Address</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($contacts as $contact) {
        $html .= '
                <tr>
                    <td><strong>' . htmlspecialchars($contact['name']) . '</strong></td>
                    <td>' . htmlspecialchars($contact['phone_number']) . '</td>
                    <td>' . htmlspecialchars($contact['relation']) . '</td>
                    <td>' . htmlspecialchars($contact['address']) . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>';
} else {
    $html .= '<div class="no-data">No emergency contacts added yet.</div>';
}

$html .= '
        <!-- Emergency Kit Section -->
        <div class="section-title">Emergency Kit Builder</div>';

if (count($kit_items) > 0) {
    $html .= '
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Category</th>
                    <th style="width: 50%;">Item Name</th>
                    <th style="width: 20%;">Quantity</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($kit_by_category as $category => $items) {
        foreach ($items as $index => $item) {
            $html .= '
                <tr>
                    <td>' . ($index === 0 ? '<strong>' . htmlspecialchars($category) . '</strong>' : '') . '</td>
                    <td>' . htmlspecialchars($item['item_name']) . '</td>
                    <td style="text-align: center;">' . htmlspecialchars($item['quantity']) . '</td>
                </tr>';
        }
    }
    
    $html .= '
            </tbody>
        </table>';
} else {
    $html .= '<div class="no-data">No emergency kit items added yet.</div>';
}

$html .= '
    </div>
    
    <div class="footer">
        <p>© Alerto ' . date('Y') . '. All Rights Reserved.</p>
        <p>This report is confidential and intended for emergency preparedness purposes only.</p>
    </div>
</body>
</html>';

// Configure DOMpdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

// Create DOMpdf instance
$dompdf = new Dompdf($options);

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Create reports directory if it doesn't exist
if (!file_exists('reports')) {
    mkdir('reports', 0777, true);
}

// Generate unique filename
$filename = 'Emergency_Preparedness_' . $user_id . '_' . date('YmdHis') . '.pdf';
$filepath = 'reports/' . $filename;

// Save PDF to reports folder
file_put_contents($filepath, $dompdf->output());

// Redirect to view PDF
header("Location: view_pdf.php?file=" . urlencode($filename));
exit();
?>