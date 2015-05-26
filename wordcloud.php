<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>D3 Word Clouds</title>
    <meta name="author" content="d4i"/>
  </head>
  <body>
  
  	<?php

   error_reporting(E_ALL & ~E_NOTICE);//NOTICEエラーを非表示にする

   /////db処理/////
   try{
      $dbh = new PDO('mysql:host=localhost;dbname=natori_web', 'root', 'gai0730');
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
   }catch(PDOException $e){
      var_dump($e->getMessage());
      exit;
   }

   //処理

   $sql = "SELECT detail FROM hatugen";
   $stmt = $dbh->query($sql);
   foreach( $stmt->fetchAll( PDO::FETCH_ASSOC ) as $test_auto ){
      $text = print_r($test_auto['detail'], TRUE);

      //切断
      $dbh = null;
  
      //$text = "私は関西大学総合情報学部に所属しています。関西外国語大学に所属しています。あなたのお名前は何ですか。関西大学。情報。情報。情報。うんこ、魂、喜びラブライブ、因縁、憎しみ憎しみ憎しみ嫌い嫌い嫌い鬱、鬱、鬱アメリカアメリカシラサギFラン大学の学園祭ラッスンゴレライ";
      $exe_path = '/usr/local/bin/mecab';

      /////pipe処理/////
      $descriptorspec = array(
         0 => array("pipe", "r"),
         1 => array("pipe", "w")
      );

      /////file処理/////
      $process = proc_open($exe_path, $descriptorspec, $pipes);
   
      if (is_resource($process)) {
         fwrite($pipes[0], $text);
         fclose($pipes[0]);  
         while(!feof($pipes[1])){
         $result[] = fgets($pipes[1]);
         }
         fclose($pipes[1]);
         proc_close($process);
      }
   }

   /////count処理/////
   exec("$exe_path $text", $result);
   $word_list_index = $word_list = array();
   foreach ($result as $val) {
      $tmp = explode(",", $val);
         $tmp = explode("\t", $tmp[0]);// $tmp[0]: 要素, $tmp[1]: 品詞
         if ($tmp[1] == '名詞') {
            $key = array_search($tmp[0], $word_list_index);
            if ($key === false) {// 新出
               $word_list[] = array('num' => 1, 'word' => $tmp[0]);
                  $word_list_index[] = $tmp[0];
            }else{// 既出
               $word_list[$key]['num'] = $word_list[$key]['num'] + 1;
            }
         }
   }
   unset($word_list_index);
   
   arsort($word_list);
   //$test = "count,word\n";
   $test[] = array("count","word");
   
   foreach ($word_list as $val) {
	  /*$flag = false;
      if($flag == false){
         $flag = true;
         $test = "count,word";
      }
	  unset($flag);*/
      //$test .= "{$val['num']},{$val['word']}\n";
	  //echo $test;
	
	  $test[] = array("{$val['num']}", "{$val['word']}");
	  //$test[] = array("{$val['word']}");
   }
   
   $fp = fopen('text.csv', 'w');
   
   foreach($test as $fields){
	   print_r($fields, TRUE);
   fputcsv($fp, $fields);
   }
   
   fclose($fp);
   ?>
  
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script src="https://rawgithub.com/jasondavies/d3-cloud/master/d3.layout.cloud.js"></script>

    <script>
      var filename = 'text.csv';

      d3.csv(filename, function(data){
        data = data.splice(0, 100);

        var width = window.innerWidth;
        var height = window.innerHeight;
        fill = d3.scale.category20(),
        maxcount = d3.max(data, function(d){ return d.count; } ),
        wordcount = data.map(function(d) { return {text: d.word, size: d.count / maxcount * 10}; });

        d3.layout.cloud().size([width, height])
        .words(wordcount)
        .padding(5)
        .rotate(function() { return ~~(Math.random() * 2) * 90; })
        .font("Impact")
        .fontSize(function(d) { return Math.sqrt(d.size) * 10; })
        .on("end", draw)
        .start();
/*
        function draw(words) {
          d3.select("body").append("svg")
          .attr({
            "width": width,
            "height": height
          })
          .append("g")
          .attr("transform", "translate(" + [ width >> 1, height >> 1 ] + ")")
          .selectAll("text")
          .data(words)
          .enter()
          .append("text")
          .style({
            "font-size": function(d) { return d.size*10 + "px"; },
            "font-family": "Impact",
            "fill": function(d, i) { return fill(i); }
          })
          .attr({
            "text-anchor": "middle",
            "transform": function(d) { return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")"; }
          })
          .on( "click", function(d){
             var str = (d.text);
             alert(str);
          })
*/
          function draw(words) {
            d3.select("body").append("svg")
              .attr({
                 "height": height,
                 "width": width,
              })
              .append("g")
              .attr("transform", "translate(" + [ width >> 1, height >> 1 ] + ")")
              .selectAll("text")
              .data(words)
              .enter()
              .append("text")
              .style({
                 "font-size": function(d) { return d.size + "px"; },
                 "font-family": "Impact",
                 "fill": function(d, i) { return fill(i); }
              })
              .attr({
                 "text-anchor": "middle",
                 "transform": function(d) { return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")"; }
              })
          .text(function(d) { return d.text; })
          .on("mouseover", function(d){
             d3.select(this).style({opacity: '0.6'});
          })
          .on("mouseout", function(d){
             d3.select(this).style({opacity: '1.0'});
          })
        };
      });
    </script>
	
  </body>
</html>