<?php
                                            require_once('mailgun/vendor/autoload.php');
                                            use Mailgun\Mailgun;
                                            
                                            $Send_Email = "hassan@gmail.com";
                                            $mylistEmail = "gatukurh1@gmail.com";
                                            $subject = "hhhhhhhh";
                                            $ch = curl_init();
                                            $my_bc_msg = "hello testing";
                                            
                                            curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/sandboxdcea917ee0fc4b5fb2b297bd7fb61ee9/messages');
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($ch, CURLOPT_POST, 1);
                                            $post = array(
                                                'from' => $Send_Email,
                                                'to' => $mylistEmail,
                                                'subject' => $subject,
                                                'text' => $my_bc_msg
                                            );
                                            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
                                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                                            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                                            curl_setopt($ch, CURLOPT_USERPWD, 'api:0f47b2cc5f2f3b472360d6110bd02813-e438c741-cc273748');
                                            
                                            $result = curl_exec($ch);
                                            if (curl_errno($ch)) {
                                                 
                                                echo 'Error:' . curl_error($ch);
                                            }else{
                                               print_r($result);
                                            }
                                            curl_close($ch);
                                            
                                            
                                            ?>