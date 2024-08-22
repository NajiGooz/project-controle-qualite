import { createBrowserRouter } from "react-router-dom";
import Layout from "../Layouts/Layout";
import PlanDeControle from "../components/PlansDeControle/PlansDeControle";
import EtapesDeProcess from "../components/PlansDeControle/EtapesDeProcess";
import AnalysesProcess from "../components/PlansDeControle/AnalysesProcess";
import Produit from "../components/DonnesDeBase/Produits";
import Analyses from "../components/DonnesDeBase/Analyses";
import Etapes from "../components/DonnesDeBase/Etapes";
import Unites from "../components/DonnesDeBase/Unites";
import ParametreAnalyses from "../components/DonnesDeBase/ParametresAnalyse";



export const router = createBrowserRouter([
    {
        element: <Layout />,
        children: [
            {
                path: "/planDeControle",
                element: <PlanDeControle />
            },
            {
                path: "planDeControle/:id",
                element: <EtapesDeProcess />
            },
            {
                path: "planDeControle/:id/:codeEtape",
                element: <AnalysesProcess />
            },
            {
                path: "/",
                element: <Produit />
            },
            {
                path: "/etapes",
                element: <Etapes />
            },
            {
                path: "/analyses",
                element: <Analyses />
            },
            {
                path: "/unites",
                element: <Unites />
            },
            {
                path: "/paramsAnalyse",
                element: <ParametreAnalyses />
            },
        ]
    }
])