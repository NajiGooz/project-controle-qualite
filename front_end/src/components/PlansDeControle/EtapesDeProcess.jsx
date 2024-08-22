import { useState, useEffect } from "react"
import axios from "axios"
import { Link, useParams } from "react-router-dom"
import useEtapes from "../../DataFolder/EtapesData"



export default function EtapesDeProcess() {

    const { id } = useParams()
    const etapes = useEtapes()
    const [plan, setPlan] = useState({})
    const [libelleProduit, setLibelleProduit] = useState('')
    useEffect(() => {
        const fetchPlan = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/planControle/${id}`);
                setPlan(response.data);  // Directly set the plan object
                setLibelleProduit(response.data.produit.libelleProduit);
            } catch (err) {
                console.error('Error fetching data:', err);
            }
        };
        fetchPlan();
    }, [id]);

    const [processFabrications, setProcessFabrications] = useState([])
    useEffect(() => {
        const fetchProcessFabrications = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/processFabrications`);
                setProcessFabrications(response.data);
            } catch (e) {
                console.error('Error fetching data:', e);
            }
        }
        fetchProcessFabrications()
    }, [])

    const filterProcess = processFabrications
        .filter(processFabrication => processFabrication.codePlan === plan.codePlan)
    console.log(plan.codePlan)
    const [processFabrication, setProcessFabrication] = useState({
        codePlan: plan.codePlan,
        orderEtape: '',
        codeEtape: '',
    })
    const [updatedProcess, setUpdatedProcess] = useState({
        codePlan: plan.codePlan,
        orderEtape: '',
        codeEtape: '',
    })

    useEffect(() => {
        if (plan.codePlan !== undefined) {
            setProcessFabrication((prevProcess) => ({
                ...prevProcess,
                codePlan: plan.codePlan
            }))
        }
    }, [plan.codePlan])

    const [messageError, setMessageError] = useState('')

    useEffect(() => {
        if (messageError) {
            const timer = setTimeout(() => {
                setMessageError('');
            }, 10000);

            return () => clearTimeout(timer);
        }
    }, [messageError]);

    const handlChangeProcessFabrication = (e) => {
        const { name, value } = e.target;
        const orderEtape = filterProcess.length === 0 ? 1 : filterProcess[filterProcess.length - 1].orderEtape + 1;
        setProcessFabrication({
            ...processFabrication,
            [name]: value,
            orderEtape: orderEtape
        });
        console.log(processFabrication)
    };
    const handlClickProcessFabrication = async (e) => {
        e.preventDefault();
        try {
            await axios.post(`http://127.0.0.1:8000/api/processFabrication/`, processFabrication)
            setProcessFabrications([
                ...processFabrications, {
                    codePlan: plan.codePlan,
                    orderEtape: processFabrication.orderEtape,
                    codeEtape: processFabrication.codeEtape
                }
            ]
            );
            setProcessFabrication({
                codePlan: plan.codePlan,
                orderEtape: '',
                codeEtape: '',
            })
        } catch (error) {
            const errModification = "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry";
            if (error.response.data.message.includes(errModification)) {
                setMessageError("Cette étape est déjà associée à ce plan de contrôle.");
            } else {
                setMessageError(error.response.data.message);
            }
        }
    }
    const [display, setDisplay] = useState('hidden');
    const buttonModifier = (etape) => {
        setDisplay('block');
        const process = filterProcess.find(process => process.codeEtape === etape);
        setUpdatedProcess({
            codePlan: plan.codePlan,
            orderEtape: process.orderEtape,
            codeEtape: process.codeEtape,
        });
    }
    const handlChangeUpdatedProcess = (e) => {
        const { name, value } = e.target;
        setUpdatedProcess({
            ...updatedProcess, [name]: value
        })
        console.log(updatedProcess)
    }
    const handleUpdateProcess = async (e, plan, etape) => {
        e.preventDefault();
        try {
            const response = await axios.put(`http://127.0.0.1:8000/api/processFabrication/${plan}/${etape}`, updatedProcess)
            setProcessFabrications(response.data.allProcess);
        } catch (error) {
            console.log(error);
            setMessageError(error.response.data.message);
        }
        setDisplay('hidden');
    }

    const deleteProcessFabrication = async (plan, etape) => {
        try {
            const response = await axios.delete(`http://127.0.0.1:8000/api/processFabrication/${plan}/${etape}`);
            setProcessFabrications(response.data.allProcess);
            setMessageError(response.message);
        } catch (error) {
            console.log(error);
            setMessageError(error.response.data.message);
        }
    }
    return <>
        <h1 className="text-4xl font-primaryBold dark:text-white">Plans de Contrôle</h1> <br />
        <div className="relative">
            <div className={`shadow-xl p-2 absolute w-[388px] h-[392px] bg-[#238899] rounded-xl ${display}`} style={{ transform: "translate(-50%, -50%)", left: "50%", top: "50%", zIndex: "999" }}>
                <svg onClick={() => setDisplay('hidden')} className="ml-auto w-[30px] h-[30px] cursor-pointer text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                <form className="w-full px-2" onSubmit={(e) => handleUpdateProcess(e, updatedProcess.codePlan, updatedProcess.codeEtape)}>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code Plan</label>
                        <input type="text" disabled name="codePlan" value={updatedProcess.codePlan} className="font-primaryRegular bg-slate-400 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_Process" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Code Etape</label>
                        <input type="text" disabled name="codeEtape" value={etapes.find(etape => etape.codeEtape === updatedProcess.codeEtape)?.libelleEtape || ''} className="font-primaryRegular bg-slate-400 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Process" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Oprder Etape</label>
                        <input type="number" name="orderEtape" min='1' onChange={handlChangeUpdatedProcess} value={updatedProcess.orderEtape} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Process" required />
                    </div>
                    <div className="flex justify-center">
                        <button type="submit" className="font-primaryBold my-2 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Modifier</button>
                    </div>
                </form>
            </div>
            <p className="text-xl font-primaryMedium text-gray-900 dark:text-white" style={{ color: "#9A93B3" }}>Plan De Controle De Produit / {libelleProduit}</p> <br />
            <p className="text-xl font-primaryBold dark:text-white">Les Etapes De Process</p> <br />
            <div className="container mx-auto px-20">
                {messageError &&
                    <div className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span className="font-medium">Error!</span> {messageError}
                    </div>
                }
                <form className="flex items-center justify-center gap-x-6" onSubmit={handlClickProcessFabrication}>
                    <div className="mb-5 w-96">
                        <p className="text-lg font-primaryBold dark:text-white">Sélectionner les Etapes par ordre:</p>
                    </div>
                    <div className="mb-5 w-96">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Etape De Process</label>
                        <select onChange={handlChangeProcessFabrication} name="codeEtape" value={processFabrication.codeEtape} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Unite" required>
                            <option value='' disabled>Selectionnez une Etape de Process</option>
                            {
                                etapes.map((etape) => {
                                    return <option key={etape.id} value={etape.codeEtape}>{etape.libelleEtape}</option>
                                })
                            }
                        </select>
                    </div>
                    <button type="submit" className="font-primaryBold mt-3.5 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Selectionnez</button>
                </form>

                <div className="flex items-center p-4 mb-4 text-sm text-yellow-300 rounded-lg bg-black/60 dark:text-yellow-300" role="alert">
                    <svg className="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span className="sr-only">Info</span>
                    <div>
                        <span className="font-medium">Attention !</span> Lors de la modification ou de la suppression d&apos;une étape, veuillez prêter une attention particulière à l&apos;ordre des étapes.
                    </div>
                </div>
                <div className="shadow-xl relative overflow-x-auto rounded-lg">
                    <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Code Plan
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold">
                                    Etape de Process
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                    Order  Etape
                                </th>
                                <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                filterProcess.map((process, key) => {
                                    return <tr key={key} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" className="px-6 py-4 font-primaryBold text-gray-900 whitespace-nowrap dark:text-white">
                                            {process.codePlan}
                                        </th>
                                        <td className="px-6 py-4 font-primaryMedium">
                                            {
                                                etapes.filter((etape) => etape.codeEtape == process.codeEtape).map(etape => etape.libelleEtape)
                                            }
                                        </td>
                                        <td scope="row" className="px-6 py-4 text-center font-primaryBold text-gray-900 whitespace-nowrap dark:text-white">
                                            {process.orderEtape}
                                        </td>
                                        <td className="px-6 py-4 flex justify-center gap-x-4">
                                            <Link to={`${process.codeEtape}`} className="font-primaryBold text-yellow-300">Analyses</Link>
                                            <button onClick={() => buttonModifier(process.codeEtape)} className="font-primaryBold text-blue-600">modifier l&apos;order</button>
                                            <button onClick={() => deleteProcessFabrication(process.codePlan, process.codeEtape)} className="font-primaryBold text-red-600">Supprimer</button>
                                        </td>
                                    </tr>
                                })
                            }
                        </tbody>
                    </table>
                </div>
                    <br/>
            </div>
        </div>

    </>
}