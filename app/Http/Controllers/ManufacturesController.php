<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\GoodManufacture;
use App\Models\Manufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manufactures = Manufacture::all();

        return view('manufactures.manufactures', [
            'manufactures' => $manufactures
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $goods = Good::all();

        return view('manufactures.manufactures_form', [
            'goods' => $goods
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $manufacture = Manufacture::create([
            'name' => $request->name
        ]);

        $insertedId = $manufacture->id;

        foreach ($request->goods as $key => $value) {
            GoodManufacture::create([
                'good_id' => $value,
                'manufacture_id' => $insertedId
            ]);
        }

        return redirect()->route('manufactures.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Manufacture $manufacture
     * @return \Illuminate\Http\Response
     */
    public function edit(Manufacture $manufacture)
    {

        $id = $manufacture->id;

        $selectedGoods = DB::table('goods')
            ->select('goods.id', 'goods.name')
            ->leftJoin('good_manufacture', 'goods.id', '=', 'good_manufacture.good_id')
            ->where('good_manufacture.manufacture_id', '=', $id)
            ->get();

        $goods = Good::all();

        return view('manufactures.manufactures_form', [
            'goods' => $goods,
            'manufacture' => $manufacture,
            'selectedGoods' => $selectedGoods
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Manufacture $manufacture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Manufacture $manufacture)
    {
        $id = $manufacture->id;

        DB::table('manufactures')
            ->where('id', $id)
            ->update(['name' => $request->name]);

        DB::table('good_manufacture')->where('manufacture_id', '=', $id)->delete();

        foreach ($request->goods as $key => $value) {
            GoodManufacture::create([
                'good_id' => $value,
                'manufacture_id' => $id
            ]);
        }

        return redirect()->route('manufactures.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Manufacture $manufacture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manufacture $manufacture)
    {
        $manufacture->delete();

        return redirect()->route('manufactures.index');
    }
}