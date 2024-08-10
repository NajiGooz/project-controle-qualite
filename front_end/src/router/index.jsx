import { createBrowserRouter } from "react-router-dom";
import Layout from "../Layouts/Layout";
import DonnesDeBase from "../components/DonnesDeBase";
import PlanDeControle from "../components/PlansDeControle/PlansDeControle";



export const router = createBrowserRouter([
    {
        element: <Layout />,
        children: [
            {
                path: "/",
                element: <DonnesDeBase />
            },
            {
                path: "/PlanDeControle",
                element: <PlanDeControle />
            },
        ]
    }
])