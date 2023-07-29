<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


// FETCH EDIT CONTAINERS
if( isset($_POST['action']) && ($_POST['action'] == "getFetchEditCont") ) {
    
    $user_id = $_POST['user_id'];
    $pipe_id = $_POST['pipe_id'];
    
    if($user_id === $wo['user']['user_id'] ){ ?>
        
       <div class="mt-3 mb-5 mdbdy">
          <div class="container-fluid">
			<div class="scrolling-wrapper row m-auto flex-row flex-nowrap pb-4 pt-5" id="newRow">
			    
			    <?php 
			        $pipeStages = GetPipeLineStages($pipe_id);
			        $count = 0;
			        foreach($pipeStages as $st){ ?>
			        
					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="stgmn stag <?php if($count==0){ echo "active"; } ?>"  data-sid="<?= $st['id'] ?>" data-sno="<?= $st['s_no'] ?>" id="stage_<?= $st['id'] ?>_id" data-id="stage_no_<?= $st['s_no'] ?>">
							<div class="d-flex justify-content-between w-100 tostg">
								<div class="d-flex align-items-center stgcnt">
									<span class="nomnm">Stage</span> <span class="ms-2 nom"><?= $st['s_no'] ?></span>
								</div>
								
								<div class="nvstg" >
									
									<a href="javascript:void(0)" onclick="ShowStageModal(<?= $st['id'] ?>,<?= $pipe_id ?>,<?= $st['s_no'] ?>)" data-backdrop="static" data-keyboard="false"><i class="fas fa-ellipsis-h"></i></a>
								</div>
							</div>
							
							<div class="midstg mb-5">
								<h4 class="text-uppercase mb-4 " id="stage_<?= $st['id'] ?>_N"><?= $st['name'] ?></h4>
								<p id="stage_<?= $st['id'] ?>_D"><?= $st['description'] ?>.</p>
							</div>
							
							<div class="text-center position-relative btmstg">
								<p class="m-0"><span id="NumStepof_<?= $st['id'] ?>_No"><?= $st['steps']['stno'] ?></span> Steps</p>
								
								<?php if($count == 0) { ?>
								    <a href="javascript:void(0)" id="stageOpenAndClose"><i class="fa fa-chevron-down"></i></a>
								<?php }else{ ?>
								    <a href="javascript:void(0)" id="stageOpenAndClose"><i class="fa fa-chevron-up"></i></a>
								<?php } ?>
								
							</div>
						</div>
					</div>
					
                <?php $count++; } ?>
					
				
				<div class="order-3 col-xl-3 col-lg-3 col-md-3 col-sm-3 col-sm-12">
					<div class="stgmn">
						<div class="align-items-center align-content-center d-flex w-100 midact" style="min-height: 168px;" id="addRow">
							<div class="m-auto plsicn">
								<i class="fas fa-plus"></i>
							</div>							
						</div>
						
						
					</div>
				</div>
				
		    </div>
	 
        </div>
	   </div>
	  
	   <div class="mb-5 drgdrp">
    	  	<div class="container-fluid">
    		  	<div class="row">
    				<div class="col-sm-12">
    					<div class="mb-4 ttl">
    						<h3 class="fw-bold text-center">Drag actions to your steps below</h3>
    					</div>
    					<div class="text-center drpin">
    						<ul class="p-0 m-0">
    							<li class="d-xl-inline-block d-lg-inline-block d-none"><img src="<?= $wo['config']['site_url'] ?>/design/img/la.png" alt="" width="100"></li>
    							
    							<?php $steaction = GetStepDragActions(); foreach($steaction as $sa) { ?>
        							<li class="d-inline-block dragA" id="<?= $sa['id'] ?>" data-modal-name="<?= $sa['modal'] ?>">
        								<a href="javascript:void(0)" data-toggle="modal" data-num= "<?= $sa['no'] ?>" data-backdrop="static" data-keyboard="false">
        									<i class="<?= $sa['icon'] ?>"></i>
        									<p><?= $sa['name'] ?></p>
        								</a>
        							</li>
    							<?php } ?>
    							
    							<li class="d-xl-inline-block d-lg-inline-block d-none"><img src="<?= $wo['config']['site_url'] ?>/design/img/ra.png" alt="" width="100"></li>
    						</ul>
    					</div>
    				</div>
    			</div>
    		</div>
	    </div>
	  
	   <div class="mt-3 mb-5 mdbdy">
	  	    <div class="container-fluid">
			    <div class="scrolling-wrapper row m-auto flex-row flex-nowrap pb-4 pt-5 awad" id="newRow2">
    			    
    			    <?php $query2 = selectFirstStageSteps(1,$pipe_id); foreach($query2 as $ste){ ?>
					
                    <div style="position:static !important;" class="col-xl-3 col-lg-3 col-md-3 col-sm-12 dropRow ui-droppable" data-action-id="<?= $ste['id'] ?>" id="stepss">
            		    <div class="stgmn ste">
            				<div class="d-flex justify-content-between w-100 tostg">
            					<div class="d-flex align-items-center stgcnt">
            						<span class="nomnm">Step</span> <span class="ms-2 nom"><?= $ste['step_no'] ?></span>
            					</div>
            					
            					<div class="nvstg">
            					    
            						<a href="javascript:void(0)" onclick="ShowStepModal(<?= $ste['step_no'] ?>,<?= $ste['stage_id'] ?>,<?= $ste['id'] ?>)" data-backdrop="static" data-keyboard="false"><i class="fas fa-ellipsis-h"></i></a>
            						
            					</div>
            				</div>
            				
            				<div class="midstg mb-5">
            					<h4 class="text-uppercase mb-4 " id="step_<?= $ste['id'] ?>_N"><?= $ste['name'] ?></h4>
            					<p></p>
            				</div>
            				
            				<div class="collapse" data-parent="#newStepRows" id="collapseExample_<?= $ste['id'] ?>">
              					<div class="card12 card-bod21y">
            						<div class="text-center drpin clps">
                            			<ul class="p-0 m-0 " id="stepListAction">
                            			    
                            			   <?php $actStep = GetAllActionInAStep($ste['id']); foreach($actStep as $sat) { $asd = GetSingleStepActDetails($sat['type']); ?>
                            			   
                            			 
                            			    <li class="d-block">
                								<a class="p-3 d-flex w-100 justify-content-between align-items-center" data-step-id="<?= $sat['step_id'] ?>" data-step-actionid="<?= $sat['type'] ?>" data-action-no="<?= $sat['action_no'] ?>" href="javascript:void(0)" onclick="getAllModalActionDetailsF(<?= $sat['step_id'] ?>,<?= $sat['id'] ?>,<?= $sat['type'] ?>)" data-backdrop="static" data-keyboard="false">
                									<div class="">										
                										<p><i class="<?= $asd['icon'] ?>"></i> <?= $asd['name'] ?></p>
                									</div>
                									
                									<div class="">
                										<i class="fas fa-ellipsis-v"></i>
                									</div>
                								</a>
                							</li>
                							
                							<?php } ?>
                						
                							
                							
                            			</ul>
            		                </div>
            					</div>
            				</div>
            				
            				<div class="text-center position-relative btmstg">
            					<p class="m-0"><span id="actionNo"><?= $ste['actions'] ?></span> Actions</p>
            					<a class="openstep" data-stid="<?= $ste['id'] ?>" data-toggle="collapse" href="#collapseExample_<?= $ste['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseExample" id="stepOpenAndClose"><i class="fa fa-chevron-up"></i></a>
            				</div>
            			</div>
            		</div>
            		
            		<?php } ?>
            		
            	
    				<div style="float:left;" class="order-3 col-xl-3 col-lg-3 col-md-3 col-sm-12">
    					<div class="stgmn">
    						<div class="align-items-center align-content-center d-flex w-100 midact" style="min-height: 168px;" id="addRow2">
    							<div class="m-auto plsicn">
    								<i class="fas fa-plus"></i>
    							</div>							
    						</div>
    						
    						
    					</div>
    				</div>
    				
    			</div>
			 
		  	
		  </div>
	    </div>
        
<?php        
        
        
 }else{
        
        
        	
  }
    
    	
    
    
}