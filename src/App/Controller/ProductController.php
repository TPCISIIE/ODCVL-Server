<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\Product;
use App\Model\Property;
use Respect\Validation\Validator as V;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProductController extends Controller
{
    /**
     * Add product
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function add(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $this->validator->validate($request, [
                'name' => V::notBlank()
            ]);

            $category = Category::find($request->getParam('category_id'));
            $propertiesIds = $request->getParam('properties');
            $properties = $propertiesIds ? Property::whereIn('id', $propertiesIds)->get() : null;

            if (!$category) {
                $this->validator->addError('category_id', 'La catégorie n\'existe pas');
            }

            if ($propertiesIds && count($propertiesIds) != $properties->count()) {
                $this->validator->addError('properties', 'Une ou plusieurs propriétés n\'existent pas');
            }

            if ($this->validator->isValid()) {
                $product = new Product([
                    'name' => $request->getParam('name')
                ]);

                $product->save();
                $product->categories()->attach($category);

                if ($properties) {
                    $product->properties()->attach($properties);
                }

                $this->flash('success', 'Produit "' . $product->name . '" ajouté');
                return $this->redirect($response, 'product.get');
            }
        }

        return $this->view->render($response, 'Product/add.twig', [
            'categories' => Category::all(),
            'properties' => Property::all()
        ]);
    }

    /**
     * Edit product
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function edit(Request $request, Response $response, $id)
    {
        $product = Product::with(['categories', 'properties'])->find($id);

        if (!$product) {
            throw $this->notFoundException($request, $response);
        }

        if ($request->isPost()) {
            $this->validator->validate($request, [
                'name' => V::notBlank()
            ]);

            $category = Category::find($request->getParam('category_id'));
            $propertiesIds = $request->getParam('properties');
            $properties = $propertiesIds ? Property::whereIn('id', $propertiesIds)->get() : null;

            if (!$category) {
                $this->validator->addError('category_id', 'La catégorie n\'existe pas');
            }

            if ($propertiesIds && count($propertiesIds) != $properties->count()) {
                $this->validator->addError('properties', 'Une ou plusieurs propriétés n\'existent pas');
            }

            if ($this->validator->isValid()) {
                $product->name = $request->getParam('name');
                $product->save();

                $product->categories()->detach();
                $product->categories()->attach($category);

                $product->properties()->detach();
                if ($properties) {
                    $product->properties()->attach($properties);
                }

                $this->flash('success', 'Produit "' . $product->name . '" modifié');
                return $this->redirect($response, 'product.get');
            }
        }

        return $this->view->render($response, 'Product/edit.twig', [
            'product' => $product,
            'categories' => Category::all(),
            'properties' => Property::all()
        ]);
    }

    /**
     * Delete product
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function delete(Request $request, Response $response, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            throw $this->notFoundException($request, $response);
        }

        $items = array_column($product->items->toArray(), 'id');
        if (!empty($items)) {
            DB::table('item_property')->whereIn('item_id', $items)->delete();
        }

        $product->items()->delete();
        $product->properties()->detach();
        $product->categories()->detach();
        $product->delete();

        $this->flash('success', 'Produit "' . $product->name . '" supprimé');
        return $this->redirect($response, 'product.get');
    }

    /**
     * Get product properties
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function getProperties(Request $request, Response $response, $id)
    {
        $product = Product::with(['categories', 'properties'])->find($id);

        if (!$product) {
            throw $this->notFoundException($request, $response);
        }

        $properties = $product->getProperties();

        return $this->view->render($response, 'Product/properties.twig', [
            'properties' => $properties
        ]);
    }

    /**
     * Get products list
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function get(Request $request, Response $response)
    {
        return $this->view->render($response, 'Product/get.twig', [
            'products' => Product::all()
        ]);
    }
}
