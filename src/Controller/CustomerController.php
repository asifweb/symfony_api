<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Customer;

/**
 * @Route("customer", name="customer.")
 */
class CustomerController extends AbstractController
{
    private $customerRepository;
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $data = [];
        $customers = $this->customerRepository->findAll();
        foreach ($customers as $key => $customer) {
            $data[] = $customer->toArray();
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/add", name="add", methods={"POST"})
     * @param  mixed $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $phoneNumber = $data['phoneNumber'];
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting Mandtory Fields');
        }
        $isEmailExists = $this->customerRepository->isEmailExists($email);
        if (!$isEmailExists) {
            $this->customerRepository->saveCustomer($firstName, $lastName, $email, $phoneNumber);
            $response = [
                'status' => true,
                'message' => 'Customer Saved'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Email Already Exists'
            ];
        }
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Route("/show/{id}", name="show", methods={"GET"})
     * @param  Post $post
     * @return void
     */
    public function show($id): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);
        $result = [
            'status' => false,
            'message' => 'Data not found'
        ];
        if (!empty($customer)) {
            $result = $customer->toArray();
        }
        return new JsonResponse($result, Response::HTTP_OK);
    }
    /**
     * @Route("/update/{id}" , name="update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['firstName']) ? true : $customer->setFirstName($data['firstName']);
        empty($data['lastName']) ? true : $customer->setLastName($data['lastName']);
        empty($data['email']) ? true : $customer->setEmail($data['email']);
        empty($data['phoneNumber']) ? true : $customer->setPhoneNumber($data['phoneNumber']);

        $isEmailExists = $this->customerRepository->isEmailExists($data['email']);
        if (!$isEmailExists) {
            $updatedCustomer = $this->customerRepository->updateCustomer($customer);
            $response = [
                'status' => true,
                'message' => 'Customer Updated',
                'data' => $updatedCustomer->toArray()
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Email Already Exists'
            ];
        }
        return new JsonResponse($response, Response::HTTP_CREATED);
    }
    /**
     * @Route("/remove/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(Customer $customer)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($customer);
        $em->flush();
        $response = [
            'status' => true,
            'message' => 'Customer Removed'
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
