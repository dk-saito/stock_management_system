<?php

$tmpl_dir = "./tmpl";

// 入力内容を変数に代入

$name = $_POST["name"];
$stock = $_POST["stock"];
$price = $_POST["price"];

// 文字コードの処理
$name = mb_convert_encoding($name, "utf-8");
$stock = mb_convert_encoding($stock, "utf-8");
$price = mb_convert_encoding($price, "utf-8");

// 特殊文字の処理
$name = htmlentities($name, ENT_QUOTES, "UTF-8");
$stock = htmlentities($stock, ENT_QUOTES, "UTF-8");
$price = htmlentities($price, ENT_QUOTES, "UTF-8");

// データベースに接続
$dsn = 'mysql:host=localhost; dbname=stock_management_system; charset=utf8';
$user = 'dk-saito';
$pass = 'd1a1i1k9i---';

try{
  $db = new PDO($dsn, $user, $pass);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if ($db == null){
    echo "接続に失敗しました。";
  }else{
    // INSERT文の定義
    if($name != ""){
      $sql = "INSERT INTO products (product_name, product_stock, product_price) VALUES (:name, :stock, :price)";
      //  プリペアードステートメント
      $stmt = $db->prepare($sql);
  
      // bindParamによるパラメータ－と変数の紐付け
      $stmt -> bindParam(':name', $name);
      $stmt -> bindParam(':stock', $stock);
      $stmt -> bindParam(':price', $price);
  
      // INSERTの実行
      $stmt->execute();
      echo("<p>data was successfully added to table \"stock\"</p>");
      
    }
  }
}catch (PDOException $e){
  echo('エラー内容：'.$e->getMessage());
  die();
}
// $db = null;


// データベースに格納されている商品一覧の表示
product_search();

function product_search(){
	// global $in;
	global $db;
	global $name;
	// global $tmpl_dir;

	# 自身のパス
	$script_name=$_SERVER['SCRIPT_NAME'];

	# SQLを作成
	// $query = "SELECT * FROM products WHERE product_flag = 1";
	$query = "SELECT * FROM products";
	
	# プリペアードステートメントを準備
	$stmt = $db->prepare($query);
	$stmt->execute();
  echo "a";
  // var_dump($stmt);
  
	$product_data = "";	
	while($row = $stmt->fetch()){
    // var_dump($row);
		$product_id = $row['product_id'];
		$product_data .= "<tr>";
		$product_data .= "<td class=\"form-left\">$product_id</td>";
		$product_data .= "<td class=\"form-left\">$row[product_name]</td>";
		$product_data .= "<td class=\"form-left\">$row[product_price]</td>";
		$product_data .= "</tr>\n";
	}
   $tmpl = page_read("product.tmpl");
   $tmpl = str_replace("!product_data!",$product_data,$tmpl);
   echo $tmpl;

	// if($name != ""){
	// 	# 選択した商品IDに対応する情報を取得
	// 	$stmt = $db->prepare('SELECT * FROM products WHERE product_id = :product_id');
	// 	$stmt->bindParam(':product_id', $product_id);
	// 	$product_id = $in["product_id"];
	// 	$stmt->execute();
	// 	$row = $stmt->fetch();
	// 	$product_name = $row["product_name"];
	// 	$product_price = $row["product_price"];
		
	// 	# 掲示板テンプレート読み込み
	// 	$tmpl = page_read("products_list.tmpl");
	// 	# 文字変換
	// 	$tmpl = str_replace("!product_id!",$in["product_id"],$tmpl);
	// 	$tmpl = str_replace("!product_name!",$product_name,$tmpl);
	// 	$tmpl = str_replace("!product_price!",$product_price,$tmpl);
	// 	$tmpl = str_replace("!product_data!",$product_data,$tmpl);
  //   }else{
  //   	# 掲示板テンプレート読み込み
  //   	$tmpl = page_read("product.tmpl");
  //   	# 文字変換
  //   	$tmpl = str_replace("!product_data!",$product_data,$tmpl);
	// }
	// echo $tmpl;
	// exit;
}


#-----------------------------------------------------------
# ページ読み取り
#-----------------------------------------------------------
function page_read($page){
	global $tmpl_dir;
	
	# テンプレート読み込み
	$conf = fopen( "$tmpl_dir/{$page}", "r") or die;
	$size = filesize("$tmpl_dir/{$page}");
	$tmpl = fread($conf, $size);
	fclose($conf);
	
	return $tmpl;
}


?>