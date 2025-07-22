<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImagePathController extends Controller
{
    public function fixImagePath()
    {
        $rowsAffected = 0;
        $oldDomainName = "http://back.digimediamkt.com";
        $newDomainName = "https://back.digimedia-marketing.com";

        $replaceDomain = fn($col) => "REPLACE(REPLACE({$col}, '{$oldDomainName}', '{$newDomainName}'), '/storage/app/public', '/storage')";

        try {
            DB::beginTransaction();
            
            $rows = DB::table('blog_heads')
                ->where('public_image', 'like', "$oldDomainName%")
                ->update([
                    'public_image' => DB::raw($replaceDomain('public_image'))
                ]);

            $rowsAffected += $rows;
            
            $rows = DB::table('cards')
                ->where('public_image', 'like', "$oldDomainName%")
                ->update([
                    'public_image' => DB::raw($replaceDomain('public_image'))
                ]);

            $rowsAffected += $rows;

            $rows = DB::table('blog_bodies')
                ->where(function($q) use ($oldDomainName) {
                    $q->where('public_image1', 'like', "$oldDomainName%")
                    ->orWhere('public_image2', 'like', "$oldDomainName%")
                    ->orWhere('public_image3', 'like', "$oldDomainName%");
                })
                ->update([
                    'public_image1' => DB::raw($replaceDomain('public_image1')),
                    'public_image2' => DB::raw($replaceDomain('public_image2')),
                    'public_image3' => DB::raw($replaceDomain('public_image3'))
                ]);
            
            $rowsAffected += $rows;
            
            $rows = DB::table('blog_footers')
                ->where(function($q) use ($oldDomainName) {
                    $q->where('public_image1', 'like', "$oldDomainName%")
                    ->orWhere('public_image2', 'like', "$oldDomainName%")
                    ->orWhere('public_image3', 'like', "$oldDomainName%");
                })
                ->update([
                    'public_image1' => DB::raw($replaceDomain('public_image1')),
                    'public_image2' => DB::raw($replaceDomain('public_image2')),
                    'public_image3' => DB::raw($replaceDomain('public_image3'))
                ]);
            
            $rowsAffected += $rows;
            
            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => "Error",
                'description' => $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => "Rows updated",
            'affected' => $rowsAffected
        ], 200);
    }
}
