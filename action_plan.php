<?php
$root = __DIR__;

require_once('assets/init.php');

global $wo, $sqlConnect;

showPhpErrors();

$action = filter('action');
$status = 0;
if ($action == 'addPlan') {
    $plan_title = filter('plan_title');
    $plan_description = filter('plan_description');
    $user_id = filter('user_id');
    $parent_plan = filter('parent_plan');
    $plan_thumb = filterUpload('plan_thumb');

    if (!$user_id) {
        $message = "Please login first to create plan";
    } else if (!$plan_title) {
        $message = "Please enter a valid plan title";
    } else if (!$plan_description) {
        $message = "Please enter a valid plan description";
    } else if (!$parent_plan && !$plan_thumb) {
        $message = "Please select a valid plan thumbnail";
    } else if (!$parent_plan && !isset($plan_thumb['type']) && !in_array($plan_thumb['type'], array("image/png", "image/jpeg", "image/gif"))) {
        echo 'Please upload a valid image';
    } else {

        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if (!$user) {
            $message = "User not found. Please contact administrator.";
        } else {

            $name = "";
            if (isset($plan_thumb['name']) && $plan_thumb['name']) {
                $name = date('Y-m-d-H-i-s') . "." . pathinfo($plan_thumb['name'], PATHINFO_EXTENSION);
                $directory =  "upload/action-plans/";

                if (!file_exists($directory)) {
                    mkdir($directory);
                }

                move_uploaded_file($plan_thumb['tmp_name'], $directory . $name);
            }

            $planData = [
                'plan_title' => $plan_title,
                'plan_description' => $plan_description,
                'user_id' => $user_id,
                'parent_plan' => $parent_plan,
                'plan_thumb' => $name,

            ];

            $query = insertRow('wo_action_plans', $planData);
            if ($sqlConnect->query($query)) {
                if ($parent_plan) {
                    $title = "Action";
                }
                $message = "{$plan_title} created successfully.";
                $status = 1;
            } else {
                $message = mysqli_error($sqlConnect);
            }
        }
    }


    $data = [
        'message' => $message,
        'status' => $status
    ];
} else if ($action == 'editPlan') {
    $plan_title = filter('plan_title');
    $plan_description = filter('plan_description');
    $plan_id = filter('plan_id');
    $plan_thumb = filterUpload('plan_thumb');

    if (!$plan_id) {
        $message = "Please select a valid plan";
    } else if (!$plan_title) {
        $message = "Please enter a valid plan title";
    } else if (!$plan_description) {
        $message = "Please enter a valid plan description";
    } else if ($plan_thumb && !isset($plan_thumb['type']) && !in_array($plan_thumb['type'], array("image/png", "image/jpeg", "image/gif"))) {
        $message = 'Please upload a valid image';
    } else {

        $where = ['id' => $plan_id];
        $plan = getTableData('wo_action_plans', $where, 1);
        if (!$plan) {
            $message = "Plan is deleted please try with another plan.";
        } else {


            $name = $plan['plan_thumb'];
            if ($plan_thumb) {
                $name = date('Y-m-d-H-i-s') . "." . pathinfo($plan_thumb['name'], PATHINFO_EXTENSION);
                $directory =  "upload/action-plans/";

                if (!file_exists($directory)) {
                    mkdir($directory);
                }

                $directory .= $name;

                if (move_uploaded_file($plan_thumb['tmp_name'], $directory)) {
                }
            }


            $planData = [
                'plan_title' => $plan_title,
                'plan_description' => $plan_description,
            ];

            if (isset($plan_thumb['name']) && $plan_thumb['name']) {
                $planData['plan_thumb'] = $name;
            }


            $query = updateRow('wo_action_plans', $planData, $where);
            if ($sqlConnect->query($query)) {
                $message = "Plan updated successfully.";
                $status = 1;
            } else {
                $message = mysqli_error($sqlConnect);
            }
        }
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
} else if ($action == 'get_sub_actions') {
    $parent_plan = filter('parent_plan');
    $user_id = filter('user_id');

    $html = $message = "";
    if (!$parent_plan) {
        $message = "Please select a valid plan.";
    } else if (!$user_id) {
        $message = "Please login to get sub plans.";
    } else {
        $subPlansData = [
            'user_id' => $user_id,
            'parent_plan' => $parent_plan
        ];

        $plans = getTableData('wo_action_plans', $subPlansData);
        $status = 1;
        $html .= '<option value="">Select Sub Plan</option>';

        if (count($plans)) {
            foreach ($plans as $plan) {
                $html .= "<option value='" . $plan['id'] . "'>" . $plan['plan_title'] . "</option>";
            }
        }
    }

    $data = [
        'message' => $message,
        'status' => $status,
        'sub_plans' => $html
    ];
} else if ($action == 'deletePlan') {
    $plan_id = filter('plan_id');

    if (!$plan_id) {
        $message = "Please select a valid plan";
    } else {

        $where = ['id' => $plan_id];
        $plan = getTableData('wo_action_plans', $where, 1);
        if (!$plan) {
            $message = "Plan is deleted please try with another plan.";
        } else {

            $query = deleteRow('wo_action_plans', $where);
            $title = "Plan";
            if ($sqlConnect->query($query)) {
                if ($plan['parent_plan']) {
                    $title = "Action";
                }
                $message = "{$title} delete successfully.";
                $status = 1;
            } else {
                $message = mysqli_error($sqlConnect);
            }
        }
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
} else if ($action == 'addAction') {
    $action_title = filter('action_title');
    $action_description = filter('action_description');
    $video_link = filter('video_link');
    $plan_id = filter('plan_id');
    $action_id = filter('action_id');
    $parent_plan = filter('parent_plan');
    $user_id = filter('user_id');
    $sub_action_id = filter('sub_action_id');


    if (!$plan_id) {
        $message = "Please select a valid plan";
    } else if (!$action_id) {
        $message = "Please select a valid action";
    } else if (!$action_title) {
        $message = "Please enter a valid action title";
    } else if (!$action_description) {
        $message = "Please enter a valid action description";
    } else if (!$video_link) {
        $message = "Please enter a valid action video link";
    } else {

        $plan = getTableData('wo_action_plans', ['id' => $plan_id], 1);
        if (!$plan) {
            $message = "The plan you have selected is delete. Please try again with different plan.";
        } else {

            $action = getTableData('wo_action_plans', ['id' => $action_id], 1);
            if (!$action) {
                $message = "The action you have selected is delete. Please try again with different plan.";
            } else {
                $where = [
                    'id' => $sub_action_id
                ];

                $actionData = [
                    'action_title' => $action_title,
                    'action_description' => $action_description,
                    'video_link' => $video_link,
                    'plan_id' => $plan_id,
                    'action_id' => $action_id,
                    'user_id' => $user_id,
                ];

                if ($sub_action_id) {
                    $query = updateRow('wo_plan_actions', $actionData, $where);
                    $title = "Updated";
                } else {
                    $query = insertRow('wo_plan_actions', $actionData);
                    $title = "Created";
                }

                if ($sqlConnect->query($query)) {
                    $message = "Sub Action {$title} Successfully.";
                    $status = 1;
                } else {
                    $message = mysqli_error($sqlConnect);
                }
            }
        }
    }


    $data = [
        'message' => $message,
        'status' => $status
    ];
} else if ($action == 'deleteAction') {
    $action_id = filter('action_id');

    if (!$action_id) {
        $message = "Please select a valid action";
    } else {

        $where = ['id' => $action_id];
        $action = getTableData('wo_plan_actions', $where, 1);
        if (!$action) {
            $message = "Action is deleted please try with another plan.";
        } else {

            $query = deleteRow('wo_plan_actions', $where);
            if ($sqlConnect->query($query)) {
                $message = "Action is deleted successfully.";
                $status = 1;
            } else {
                $message = mysqli_error($sqlConnect);
            }
        }
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}
header("Content-type: application/json");
echo json_encode($data);
die();
