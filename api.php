<?php
header('Content-Type: application/json; charset=utf-8');

const DATA_DIR = __DIR__ . '/data';
const JSON_FILE = DATA_DIR . '/data.json';
const XML_FILE  = DATA_DIR . '/data.xml';

if (!file_exists(DATA_DIR)) { mkdir(DATA_DIR, 0775, true); }

// Initialize storage if missing
if (!file_exists(JSON_FILE)) {
  $init = ['last_id' => 0, 'items' => []];
  file_put_contents(JSON_FILE, json_encode($init, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
  write_xml($init['items']); // create empty XML mirror
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
  case 'add':
    add_item();
    break;
  case 'edit':
    edit_item();
    break;
  case 'list':
  default:
    list_items();
    break;
}

function load_data() {
  $json = file_get_contents(JSON_FILE);
  $data = json_decode($json, true);
  if (!$data) { $data = ['last_id'=>0, 'items'=>[]]; }
  return $data;
}

function save_data($data) {
  $fp = fopen(JSON_FILE, 'c+');
  if (!$fp) { http_response_code(500); echo json_encode(['error'=>'Cannot open data file']); exit; }
  // Lock, truncate, write
  if (flock($fp, LOCK_EX)) {
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
  }
  fclose($fp);
  // Mirror to XML
  write_xml($data['items']);
}

function write_xml($items) {
  $dom = new DOMDocument('1.0', 'UTF-8');
  $dom->formatOutput = true;
  $root = $dom->createElement('inventory');
  foreach ($items as $it) {
    $item = $dom->createElement('item');
    $item->appendChild($dom->createElement('id', $it['id']));
    $item->appendChild($dom->createElement('product_name', htmlspecialchars($it['product_name'])));
    $item->appendChild($dom->createElement('quantity', $it['quantity']));
    $item->appendChild($dom->createElement('price', $it['price']));
    $item->appendChild($dom->createElement('total', $it['total']));
    $item->appendChild($dom->createElement('datetime', $it['datetime']));
    $root->appendChild($item);
  }
  $dom->appendChild($root);
  $dom->save(XML_FILE);
}

function sanitize_text($str) {
  $str = trim($str ?? '');
  $str = substr($str, 0, 120); 
  return $str;
}


function add_item() {
  $name = sanitize_text($_POST['product_name'] ?? '');
  $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
  $price = isset($_POST['price']) ? (float)$_POST['price'] : null;

  if ($name === '' || $quantity === null || $price === null || $quantity < 0 || $price < 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid input']);
    return;
  }

  $data = load_data();
  $data['last_id'] = ($data['last_id'] ?? 0) + 1;
  $id = $data['last_id'];

  $item = [
    'id' => $id,
    'product_name' => $name,
    'quantity' => $quantity,
    'price' => round($price, 2),
    'total' => round($quantity * $price, 2),
    'datetime' => gmdate('c') // ISO 8601 UTC
  ];

  $data['items'][] = $item;
  save_data($data);

  echo json_encode(['ok'=>true, 'item'=>$item]);
}

function edit_item() {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $name = sanitize_text($_POST['product_name'] ?? '');
  $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
  $price = isset($_POST['price']) ? (float)$_POST['price'] : null;

  if ($id <= 0 || $name === '' || $quantity === null || $price === null || $quantity < 0 || $price < 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid input']);
    return;
  }

  $data = load_data();
  $updated = false;
  foreach ($data['items'] as &$it) {
    if ((int)$it['id'] === $id) {
      $it['product_name'] = $name;
      $it['quantity'] = $quantity;
      $it['price'] = round($price, 2);
      $it['total'] = round($quantity * $price, 2);
      // keep original datetime (submitted time)
      $updated = true;
      break;
    }
  }
  if (!$updated) {
    http_response_code(404);
    echo json_encode(['error' => 'Item not found']);
    return;
  }
  save_data($data);
  echo json_encode(['ok'=>true]);
}

function list_items() {
  $data = load_data();
  $items = $data['items'] ?? [];
  // Order by datetime submitted desc
  usort($items, function($a, $b) {
    return strcmp($b['datetime'], $a['datetime']);
  });
  $sum = 0.0;
  foreach ($items as $it) { $sum += (float)$it['total']; }
  echo json_encode(['items'=>$items, 'sum_total'=>round($sum, 2), 'server_time'=>gmdate('c')]);
}
