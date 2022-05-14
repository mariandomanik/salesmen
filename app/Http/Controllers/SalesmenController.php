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

    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_COLUMN = 'created_at';

    /**
     * List Salesmen
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function index(Request $request): JsonResponse {
        if (!empty($request->page)) {
            $page = (int)$request->page;
        } else {
            $page = self::DEFAULT_PAGE;
        }

        if (!empty($request->per_page)) {
            $perPage = (int)$request->per_page;
            $includePerPageInLinks = true;
        } else {
            $perPage = self::DEFAULT_PER_PAGE;
            $includePerPageInLinks = false;
        }

        if (!empty($request->sort)) {
            $sortColumn = $request->sort;
            $includeSortInLinks = true;
        } else {
            $sortColumn = self::DEFAULT_SORT_COLUMN;
            $includeSortInLinks = false;
        }

        //fort desc sorting, column name includes - char
        if ($sortColumn[0] === '-') {
            $sortOrder = 'desc';
            $sortColumn = str_replace('-', '', $sortColumn);
        } else {
            $sortOrder = 'asc';
        }

        //if sort column does not exist
        if (!Schema::hasColumn('salesmen', $sortColumn)) {
            throw new BadRequestException();
        }

        $links = $this->generateLinks(Salesman::count(), $page, $perPage, $sortColumn, $includePerPageInLinks, $includeSortInLinks);

        $salesmen = Salesman::skip(($page - 1) * $perPage)->take($perPage)->orderBy($sortColumn, $sortOrder)->get();
        return $this->generateSalesmanResponse($salesmen, $links);
    }

    /**
     * Show Salesman by UUID
     * @param Salesman $salesman
     * @return JsonResponse JSON Response with salesman or salesmen
     */
    public function show(Salesman $salesman): JsonResponse {
        return $this->generateSalesmanResponse($salesman);
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
    public function destroy(Salesman $salesman): Response {
        $salesman->delete();
        return response(null, 204);
    }

    /**
     * Generate good response containing Salesmen data, code 200
     * @param Collection|Salesman $data
     * @param array $links
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
     * @param bool $includePerPageInLinks
     * @param bool $includeSortInLinks
     * @return string[]
     */
    private function generateLinks(int $total, int $page, int $perPage, string $sortColumn, bool $includePerPageInLinks, bool $includeSortInLinks): array {
        $url = '/salesmen?page=';
        $links = [];

        $lastPage = (int)ceil($total / $perPage);

        if ($page !== $lastPage) {
            $links['next'] = $url . ($page + 1);
            $links['last'] = $url . $lastPage;
        }

        if ($page > 1) {
            $links['first'] = $url . 1;
            $links['prev'] = $url . ($page - 1);
        }

        foreach ($links as $key => $link) {
            if ($includePerPageInLinks) {
                $links[$key] .= '?per_page=' . $perPage;
            }
            if ($includeSortInLinks) {
                $links[$key] .= '?sort=' . $sortColumn;
            }
        }

        return $links;
    }

}
