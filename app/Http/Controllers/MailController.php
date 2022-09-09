<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Mail;
use App\StockRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller {
   public function basic_email() {
      $name = array('name'=>"Virat Gandhi");
      /*Mail::send(['text'=>'mail'], $name, function($message) {
         $message->to('jerome.lopez.ge2018@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('emorej046@gmail.com','Virat Gandhi');
      });*/
      echo "Basic Email Sent. Check your inbox.";
   }
   public function html_email() {
      $data = array('name'=>"Virat Gandhi");
      /*Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel HTML Testing Mail');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });*/
      echo "HTML Email Sent. Check your inbox.";
   }
   public function attachment_email() {
      $data = array('name'=>"Virat Gandhi");
      Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Testing Mail with Attachment');
         $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
         $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });
      echo "Email Sent with attachment. Check your inbox.";
   }

   public function delreqapproved(Request $request){
      if ($request->action == 'approved') {
          $del = StockRequest::where('code', $request->code)->update(['del_req'=> 2, 'status'=>'DELETED']);
      }else if ($request->action == 'declined') {
          $del = StockRequest::where('code', $request->code)->update(['del_req'=> 3]);
      }
      return response()->json($del);
  }

  public function delapproval(Request $request){
      $code = StockRequest::where('code', $request->code)->first();
      $action = $request->action;
      if ($code) {
          if ($code->del_req == 1) {
              return view('pages.approval', compact('code', 'action'));
          }else if ($code->del_req == 2) {
              return 'Request to delete already approved';
          }
      }else{
          return 'Not found!';
      }
  }
}