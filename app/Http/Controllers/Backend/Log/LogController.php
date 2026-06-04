<?php

namespace App\Http\Controllers\Backend\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

   public function data(Request $request)
{
        $user = $request->user();

        $query = $this->model::query()->latest();

        return datatables()->of($query)

            ->addIndexColumn()

            ->addColumn('user', function ($data) {
                return optional($data->user())->name ?? '-';
            })

            ->addColumn('action', function ($data) {
                return $data->data['action'] ?? '-';
            })

            ->addColumn('keterangan', function ($data) {
                $method = $data->data['method'] ?? '-';
                $url    = $data->data['url'] ?? '-';
                return $method . ' | ' . $url;
            })

            ->addColumn('waktu', function ($data) {
                return $data->created_at
                    ? $data->created_at->format('d-m-Y H:i:s')
                    : '-';
            })

            ->addColumn('action_btn', function ($data) use ($user) {
                $button = '';

                if ($user->read) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Detail" data-action="show" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Tampilkan"><i class="fa fa-eye text-info"></i></button>';
                }

                return "<div class='btn-group'>{$button}</div>";
            })

            ->rawColumns(['action_btn'])

            ->make(true);
    }

    public function show($id)
{
    $data = $this->model::with([])->findOrFail($id);

    return view($this->view . '.show', [
        'data' => $data,
        'page' => (object)[
            'title' => 'Log Aktivitas'
        ]
    ]);
}


    public function delete($id)
    {
        $data=$this->model::find($id);
        return view($this->view.'.delete', compact('data'));
    }

    public function destroy($id)
    {
        $data=$this->model::find($id);
        if($data->delete()){
            $response=['status'=>TRUE, 'message'=>'Data berhasil dihapus'];
        }
        return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal dihapus']);
    }
}
