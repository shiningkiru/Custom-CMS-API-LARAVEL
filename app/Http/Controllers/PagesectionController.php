<?php

namespace App\Http\Controllers;
use Validator;
use App\PageSection;
use App\PageSectionProp;
use App\PagePropertyValues;
use Illuminate\Http\Request;

class PagesectionController extends Controller
{

    public function store(Request $request){
        $rules = array(
            'page_section_id' => 'required|exists:page_sections,id',
            'page_section_title' => 'required',
            'properties' => 'array',
            'properties.*.id' => 'required|exists:page_section_props,id',
            
        );   //'properties.*.value' => 'required'
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        $section=PageSection::find($request->page_section_id);
        $section->title=$request->page_section_title;
        if(is_array($request->properties)):
        $section->page_section_props()->saveMany(array_map(function($prop){
            if($prop['id'] != null)
            {
                $sectionProp= PageSectionProp::find($prop['id']);
                if($sectionProp->type == 'file'):
                    if(array_key_exists('image_file', $prop)):
                        $file=$prop['image_file'];
                        $image = uniqid().'sect.'.$file->getClientOriginalExtension();
                        $destinationPath = public_path('/uploads/section');
                        $file->move($destinationPath, $image);
                        $sectionProp->link = "/uploads/section/".$image;
                    endif;
                elseif($sectionProp->type == 'link'):
                    if(array_key_exists('value',$prop)):
                        $sectionProp->link = $prop['value'];
                    endif;
                else:
                    if(array_key_exists('value',$prop))
                        $sectionProp->value = $prop['value'];
                endif;
                // if(array_key_exists('value', $prop))
                //     $sectionProp->value = $prop['value'];
            }
            return $sectionProp;
        }, $request->properties));
        endif;
        $section->save();

        return response()->json("Section updated successfully",200);
    }

    public function show($id){
        $sections = PageSection::with('page_section_props')->where('id','=',$id)->get()->toArray();
        return $sections[0];
    }

    public function showOuterSection($id){
        $sections = PageSection::with('page_section_props')->where('id','=',$id)->get()->toArray();
        $sectionData=[];

        // converting to pure json object
        if(sizeof($sections)>0){
            $section = $sections[0];
            $sectionData['title']=$section['title'];
            $propArray=[];
            foreach($section['page_section_props'] as $prop){
                $value='';
                if($prop['type']=='link' || $prop['type']=='file')
                    $value=$prop['link'];
                else
                    $value=$prop['value'];
                $propArray[$prop['key']]=$value;
            }
            $sectionData['properties']=$propArray;
        }
        return $sectionData;
    }

    public function showOuterSectionArray(Request $request){
        $rules = array(
            'sectionsArray.*.key' => 'required',
            'sectionsArray.*.id' => 'required|exists:page_sections,id'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        $result=[];
        foreach($request->sectionsArray as $sectionObj){
            $sections = PageSection::with('page_section_props')->where('id','=',$sectionObj['id'])->get()->toArray();
            $sectionData=[];
            $section=$sections[0];
            $sectionData['title']=$section['title'];
            $propArray=[];
            foreach($section['page_section_props'] as $prop){
                $value='';
                if($prop['type']=='link' || $prop['type']=='file')
                    $value=$prop['link'];
                else
                    $value=$prop['value'];
                $propArray[$prop['key']]=$value;
            }
            $sectionData['properties']=$propArray;
            $result[$sectionObj['key']]=$sectionData;
        }
        return $result;
    }

}