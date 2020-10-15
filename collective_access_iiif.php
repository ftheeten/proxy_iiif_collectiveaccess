<?php
header('Content-Type: application/json');
try {
            $service_url="https://virtualcol.africamuseum.be/proxy_iiif/collective_access_iiif.php?uuid=";
            $url_detail="https://virtualcol.africamuseum.be/providence/pawtucket/service.php/IIIF/representation:";
			$uuid=$_REQUEST["uuid"];
			$servername = 'localhost';
            $username = '';
            $password = '';
			$db="virtual_collections";
            //On établit la connexion
            $pdo = new PDO("mysql:host=".$servername.";dbname=".$db, $username, $password);
            
            
           
			$stm=$pdo->prepare("SELECT object_id, representation_id as image_id FROM v_uuid_pic_numbers where uuid=:uuid");
			
			$stm->bindParam(":uuid", $uuid,PDO::PARAM_STR);
			$stm->execute();
			$res=$stm->fetchAll(PDO::FETCH_ASSOC);
            
           
            $seq_json=Array();
            foreach($res as $item)
            {
                
                $url_call=$url_detail.$item['image_id']."/info.json";
                
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_URL,$url_call);
                $result=curl_exec($handle);
                curl_close($handle);                
                $array = json_decode($result, true);
                $height=$array["height"];
                $width=$array["width"];
                $seq_json[]= '
                    {
                      "@id":"representation:'.$item['image_id'].'",
                      "@type":"sc:Canvas",
                      "label":"1",
                      "thumbnail":null,
                      "seeAlso":[
                        
                      ],
                      "height":3456,
                      "width":5184,
                      "images":[
                        {
                          "@id":"'.$url_detail.$item['image_id'].'",
                          "@type":"oa:Annotation",
                          "motivation":"sc:painting",
                          "resource":{
                            "@id":"'.$url_detail.$item['image_id'].'/full/!512,512/0/default.jpg",
                            "@type":"dctypes:Image",
                            "format":"image/jpeg",
                            "height":'.$height.',
                            "width":'.$width.',
                            "service":{
                              "@context":"http://iiif.io/api/image/2/context.json",
                              "@id":"'.$url_detail.$item['image_id'].'",
                              "profile":"http://iiif.io/api/image/2/level1.json"
                            }
                          },
                          "on":"representation:'.$item['image_id'].'"
                        }
                      ]
                    }
                  ';
            }
            
              $json='{
              "@context":"http://iiif.io/api/presentation/2/context.json",
              "@id":"representation:'.$uuid.'/manifest",
              "@type":"sc:Manifest",
              "label":"",
              "metadata":[
                
              ],
              "license":"",
              "logo":"",
              "related":[
                
              ],
              "seeAlso":[
                
              ],
              "service":[
                {
                  "@context":"http://iiif.io/api/search/0/context.json",
                  "@id":"'.$service_url.$uuid.'",
                  "profile":"http://iiif.io/api/search/0/search",
                  "label":"Search within this manifest",
                  "service":{
                    "@id":"'.$service_url.$uuid.'",
                    "profile":"http://iiif.io/api/search/0/autocomplete",
                    "label":"Get suggested words in this manifest"
                  }
                }
              ],
              "sequences":[
                {
                     "@id":"representation:'.$uuid.'/sequence/s0",
                      "@type":"sc:Sequence",
                      "label":"Sequence s0",
                      "rendering":[
                        
                      ],
                      "viewingHint":"paged",
                      "canvases":['.implode(",",$seq_json).']
                }
              ]
              }
              ';
              
              print($json);
} 
catch (PDOException $e) 
{
			echo 'Connection failed: ' . $e->getMessage();
}

?>