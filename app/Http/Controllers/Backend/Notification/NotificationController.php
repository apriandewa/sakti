<?php

namespace App\Http\Controllers\Backend\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view($this->view . '.index');
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function data(Request $request)
    {
        $user = $request->user();
        $data = $this->model::filterByUser();

        return datatables()->of($data)
            ->addColumn('content', function ($data) {
                return $data->data['title'];
            })
            ->addColumn('title', function ($data) {
                // kalau ada link di field data, jadikan clickable
                $link = $data->data['link'] ?? null;
                $content = $data->data['content'] ?? '-';

                // jika unread → kuning, jika read → hijau
                $color = $data->status ? 'text-success' : 'text-warning';

                if ($link) {
                    return '<a href="' . $link . '" class="' . $color . '" target="_blank">' . e($content) . '</a>';
                }
                return '<span class="' . $color . '">' . e($content) . '</span>';
            })
            ->addColumn('status', function ($data) {
                if ($data->status) {
                    return '<span class="badge bg-success">Sudah dilihat</span>';
                }
                return '<span class="badge bg-warning">Belum dilihat</span>';
            })
            ->addColumn('action', function ($data) use ($user) {
                $button = '';
                if ($user->read) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Detail" data-action="show" data-url="' . $this->url . '" data-id="' . $data->id . '" title="Tampilkan"><i class="fa fa-eye text-info"></i></button>';
                }
                if ($user->create) {
                    $button .= '<button class="btn-action btn btn-sm btn-outline" data-title="Edit" data-action="edit" data-url="' . $this->url . '" data-id="' . $data->id . '" title="Edit"> <i class="fa fa-edit text-warning"></i> </button> ';
                }
                if ($user->delete) {
                    $button .= '<button class="btn-action btn btn-sm btn-outline" data-title="Delete" data-action="delete" data-url="' . $this->url . '" data-id="' . $data->id . '" title="Delete"> <i class="fa fa-trash text-danger"></i> </button>';
                }
                return "<div class='btn-group'>" . $button . "</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'content', 'status', 'title'])
            ->make(TRUE);
    }


    public function store(Request $request)
    {
        // store data
    }

    public function show($id)
    {
        $data = $this->model::findOrFail($id);

        // kalau status masih unread, tandai jadi read
        if (!$data->status) {
            $data->update(['status' => true]);
        }

        return view($this->view . '.show', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->model::findOrFail($id);
        return view($this->view . '.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // update data
    }

    public function delete($id)
    {
        $data = $this->model::findOrFail($id);
        return view($this->view . '.delete', compact('data'));
    }

    public function destroy($id)
    {
        $data = $this->model::findOrFail($id);
        if ($data->delete()) {
            $response = ['status' => true, 'message' => 'Data berhasil dihapus'];
        }
        return response()->json($response ?? ['status' => false, 'message' => 'Data gagal dihapus']);
    }

    public function getNotification(Notification $notification)
    {
        return response()->json($notification->fetchNotification());
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->markAsRead($request->user()->id);
        return response()->json(['status' => true]);
    }

    public function getSideBarNotification()
    {
        try {
            $response['sidebar_notification'] = [
                'announcement' => 0,
                'user' => 0,
                'level' => 0
            ];

            foreach ($response['sidebar_notification'] as $code => $total) {
                $response = $this->menuRecursive(
                    $response,
                    $this->help->menu($code)->parent,
                    $total
                );
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
        return response()->json($response);
    }

    private function menuRecursive(mixed $response, $menu = null, $total = 0)
    {
        if (!is_null($menu)) {
            if (array_key_exists($menu->code, $response['sidebar_notification'])) {
                $response['sidebar_notification'][$menu->code] += $total;
            } else {
                $response['sidebar_notification'] += [$menu->code => $total];
            }
            $this->menuRecursive($response, $menu->parent, $total);
        }
        return $response;
    }
}
