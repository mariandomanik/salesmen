<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Http\Requests\StoreSalesmanRequest;
use App\Http\Requests\UpdateSalesmanRequest;
use App\Models\Salesman;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SalesmenController extends Controller {

    /**
     * Show either one Salesman by UUID or list of salesmen by request GET params: page, per_page, sort
     * @param Request $request HTTP Request
     * @param Salesman|null $salesman
     * @return JsonResponse JSON Response with salesman or salesmen
     * @throws BadRequestException
     */
    public function show(Request $request, Salesman $salesman = null): JsonResponse {
        //when getting 1 salesman by UUID
        if ($salesman) {
            return $this->generateSalesmanResponse($salesman);
        }

        //getting multiple salesmen
        if (!empty($request->page)) {
            $page = (int)$request->page;
        } else {
            $page = 1;
        }

        if (!empty($request->per_page)) {
            $perPage = (int)$request->per_page;
            $includePerPage = true;
        } else {
            $perPage = 10;
            $includePerPage = false;
        }

        if (!empty($request->sort)) {
            $sortColumn = $request->sort;
            $includeSort = true;
        } else {
            $sortColumn = 'created_at';
            $includeSort = false;
        }

        //fort desc sorting, column name includes - char
        if (str_contains($sortColumn, '-')) {
            $sortOrder = 'desc';
            $sortColumn = str_replace('-', '', $sortColumn);
        } else {
            $sortOrder = 'asc';
        }

        //if sort column does not exist
        if (!Schema::hasColumn('salesmen', $sortColumn)) {
            throw new BadRequestException();
        }

        $links = $this->generateLinks(Salesman::count(), $page, $perPage, $sortColumn, $includePerPage, $includeSort);

        $salesmen = Salesman::skip(($page - 1) * $perPage)->take($perPage)->orderBy($sortColumn, $sortOrder)->get();
        return $this->generateSalesmanResponse($salesmen, $links);
    }

    /**
     * Create and store new Salesman from POST request
     * @param StoreSalesmanRequest $request
     * @return JsonResponse
     */
    public function store(StoreSalesmanRequest $request): JsonResponse {
        $validated = $request->validated();
        $newSalesman = Salesman::create(
            [
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'prosight_id'    => $validated['prosight_id'],
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'gender'         => $validated['gender'],
                'marital_status' => $validated['marital_status'] ?? null,
                'titles_before'  => isset($validated['titles_before']) ? implode(',', $validated['titles_before']) : null,
                'titles_after'   => isset($validated['titles_after']) ? implode(',', $validated['titles_after']) : null,
            ]
        );

        return response()->json([
            'data' => $newSalesman
        ],
            201);
    }

    /**
     * Update existing Salesman by UUID
     * @param UpdateSalesmanRequest $request
     * @param Salesman $salesman
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function update(UpdateSalesmanRequest $request, Salesman $salesman): JsonResponse {
        $validated = $request->validated();

        try {
            if (isset($validated['first_name'])) {
                $salesman->first_name = $validated['first_name'];
            }

            if (isset($validated['last_name'])) {
                $salesman->last_name = $validated['last_name'];
            }

            if (isset($validated['prosight_id'])) {
                $salesman->prosight_id = $validated['prosight_id'];
            }

            if (isset($validated['email'])) {
                $salesman->email = $validated['email'];
            }

            if (isset($validated['phone'])) {
                $salesman->phone = $validated['phone'];
            }

            if (isset($validated['gender'])) {
                $salesman->gender = $validated['gender'];
            }

            if (isset($validated['marital_status'])) {
                $salesman->marital_status = $validated['marital_status'];
            }
            if (isset($validated['titles_before'])) {
                $salesman->titles_before = implode(',', $validated['titles_before']);
            }
            if (isset($validated['titles_after'])) {
                $salesman->titles_after = implode(',', $validated['titles_after']);
            }

            $salesman->save();
        } catch (\Exception $e) {
            throw new BadRequestException();
        }

        return $this->generateSalesmanResponse($salesman);
    }

    /**
     * Delete existing salesman by UUID
     * @param Salesman $salesman
     * @return Response Empty response with HTTP code 204
     */
    public function delete(Salesman $salesman): Response {
        $salesman->delete();
        return response('', 204);
    }

    /**
     * Generate good response containing Salesmen data, code 200
     * @param Collection|Salesman $data
     * @return JsonResponse
     */
    private function generateSalesmanResponse(Collection|Salesman $data, array $links = []): JsonResponse {
        $response['data'] = $data;

        if (!empty($links)) {
            $response['links'] = $links;
        }

        return response()->json($response, 200);
    }

    /**
     * Generate links for Salesmen response
     * @param int $total Total number of salesmen
     * @param int $page Current page in request
     * @param int $perPage Number of items per page
     * @param string $sortColumn Column name to sort by
     * @param bool $includePerPage Include per_page in URL
     * @param bool $includeSort Include sort in URL
     * @return string[]
     */
    private function generateLinks(int $total, int $page, int $perPage, string $sortColumn, bool $includePerPage, bool $includeSort): array {
        $url = '/salesmen?page=';

        $lastPage = (int)ceil($total / $perPage);
        $prevPage = ($page === 0 || $page === 1) ? 1 : $page - 1;
        $nextPage = ($page === $lastPage) ? $lastPage : $page + 1;

        $links = [
            'first' => $url . 1,
            'last'  => $url . $lastPage,
            'prev'  => $url . $prevPage,
            'next'  => $url . $nextPage
        ];

        foreach ($links as $key => $link) {
            if ($includePerPage) {
                $links[$key] .= '?per_page=' . $perPage;
            }
            if ($includeSort) {
                $links[$key] .= '?sort=' . $sortColumn;
            }
        }

        return $links;
    }

}
