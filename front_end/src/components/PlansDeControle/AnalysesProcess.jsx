import { useState, useEffect } from "react"
import axios from "axios"
import { Link, useParams } from "react-router-dom";
import useParametres from "../../DataFolder/ParamsData";
import useEtapes from "../../DataFolder/EtapesData";
import useAnalyses from "../../DataFolder/AnalysesData";
import useUnite from "../../DataFolder/UnitesData";



export default function AnalysesProcess() {

    const { id, codeEtape } = useParams()
    const etapes = useEtapes()
    const unites = useUnite()
    const etape = etapes.filter(etape => etape.codeEtape === codeEtape).map(etape => etape.libelleEtape)
    const [plan, setPlan] = useState({})
    const analyses = useAnalyses()
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
    const parametresData = useParametres();
    const [parametres, setParametres] = useState([])

    const [messageError, setMessageError] = useState('')
    useEffect(() => {
        if (messageError) {
            const timer = setTimeout(() => {
                setMessageError('');
            }, 10000);

            return () => clearTimeout(timer);
        }
    }, [messageError]);

    const [paramAnalysesProcess, setParamAnalysesProcess] = useState([])
    useEffect(() => {
        const fetchparamAnalyseProcess = async () => {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/paramAnalysesProcess`)
                setParamAnalysesProcess(response.data)
            } catch (err) {
                console.error('Error fetching products:', err);
            }
        }
        fetchparamAnalyseProcess()
    }, []);
    const fitlerPAP = paramAnalysesProcess.filter(PAP => PAP.analyse_process.codePlan == id && PAP.analyse_process.codeEtape == codeEtape)
    const [analyseProcess, setAnalyseProcess] = useState({
        codeAP: '',
        codeEtape: codeEtape,
        codePlan: id,
        codeAnalyse: '',
    })

    const handleChangeAnalyseProcess = (e) => {
        setAnalyseProcess({
            ...analyseProcess,
            codeAnalyse: e.target.value,
            codeAP: id + codeEtape + e.target.value
        })
    };
    const [parametresAP, setParametresAP] = useState(
        parametres.map(() => ({
            codePAP: '',
            codeAP: '',
            codeParam: '',
            valeurMax: '',
            valeurMin: '',
        }))
    );

    const handleSubmitAnalyseProcess = (e) => {
        e.preventDefault();
        // Filtrer les paramètres par l'analyse sélectionnée
        const filterParams = parametresData.filter(param => param.codeAnalyse === analyseProcess.codeAnalyse);
        setParametres(filterParams);

        // Initialiser `parametresAP` avec les valeurs par défaut pour chaque paramètre filtré
        setParametresAP(filterParams.map(param => ({
            codePAP: analyseProcess.codeAP + param.codeParam,
            codeAP: analyseProcess.codeAP,
            codeParam: param.codeParam,
            valeurMin: '', // Valeur par défaut vide
            valeurMax: ''  // Valeur par défaut vide
        })));
    };
    const handleCancelAnalyseProcess = (e) => {
        e.preventDefault();
        setAnalyseProcess({
            codeAP: '',
            codeEtape: codeEtape,
            codePlan: id,
            codeAnalyse: '',
        })
        setParametres([])
    }

    const handleChangePAP = (index, e) => {
        const { name, value } = e.target;
        const updatedParametresAP = [...parametresAP];
        updatedParametresAP[index] = {
            ...updatedParametresAP[index],
            [name]: value
        };
        setParametresAP(updatedParametresAP);
    };

    const handleSaveToDatabase = async () => {
        try {
            await axios.post('http://127.0.0.1:8000/api/analyseProcess', analyseProcess);
            try {
                await axios.post('http://127.0.0.1:8000/api/paramAnalyseProcess', parametresAP);
                setParametres([]);
                setAnalyseProcess({
                    codeAP: '',
                    codeEtape: codeEtape,
                    codePlan: id,
                    codeAnalyse: '',
                });

                const response = await axios.get('http://127.0.0.1:8000/api/paramAnalysesProcess');
                setParamAnalysesProcess(response.data);

            } catch (error) {
                console.log("error from first try catch")
                await axios.delete(`http://127.0.0.1:8000/api/analyseProcess/${analyseProcess.codeAP}`);
                if (error.response.data.message) {
                    setMessageError(error.response.data.message)
                }
                if (error.response.data.error.valeurMin) {
                    setMessageError(error.response.data.error.valeurMin);
                } else if (error.response.data.error.valeurMax) {
                    setMessageError(error.response.data.error.valeurMax);
                }
            }
        } catch (error) {
            console.log("error from second try catch")
            if (error.response.data.message === "The code a p has already been taken.") {
                setMessageError("La même analyse existe déjà à cette étape du process.");
            } else {
                setMessageError(error.response.data.message);
            }
        }
    }



    const [updatedParamAnalyseProcess, setUpdatedParamAnalyseProcess] = useState({
        codePAP: '',
        codeParam: '',
        codeAP: '',
        valeurMin: '',
        valeurMax: ''
    })

    const [display, setDisplay] = useState('hidden');
    const buttonModifier = (id) => {
        setDisplay('block');
        const paramAnalyseProcess = fitlerPAP.find(param => param.codePAP === id);
        setUpdatedParamAnalyseProcess({
            codePAP: paramAnalyseProcess.codePAP,
            codeParam: paramAnalyseProcess.codeParam,
            codeAP: paramAnalyseProcess.codeAP,
            valeurMin: paramAnalyseProcess.valeurMin,
            valeurMax: paramAnalyseProcess.valeurMax,
            analyse_process: paramAnalyseProcess.analyse_process,
            parametre_analyse: paramAnalyseProcess.parametre_analyse
        });
        console.log('updatedPAP', updatedParamAnalyseProcess)
    }
    const handlChangeUpdatedParam = (e) => {
        const { name, value } = e.target;
        setUpdatedParamAnalyseProcess({
            ...updatedParamAnalyseProcess, [name]: value
        })
        console.log('updatedPAP', updatedParamAnalyseProcess)
    }
    const handleUpdateParam = async (e, codePAP) => {
        e.preventDefault();
        try {
            await axios.put(`http://127.0.0.1:8000/api/paramAnalyseProcess/${codePAP}`, updatedParamAnalyseProcess)
            setParamAnalysesProcess(fitlerPAP.map(param => param.codePAP === codePAP ? updatedParamAnalyseProcess : param));
        } catch (error) {
            console.log(error);
            setMessageError(error.response.data.message);
        }
        setDisplay('hidden');
    }

    const deleteParamAnalyseProcess = async (params) => {
        try {
            // Await all delete requests for params
            await Promise.all(params.map(param =>
                axios.delete(`http://127.0.0.1:8000/api/paramAnalyseProcess/${param.codePAP}`)
            ));

            // Update the state after all requests have been completed
            setParamAnalysesProcess(prevParams =>
                prevParams.filter(parametreAnalyse => !params.some(param => param.codePAP === parametreAnalyse.codePAP))
            );

            // Await the final delete request
            await axios.delete(`http://127.0.0.1:8000/api/analyseProcess/${params[0].codeAP}`);
        } catch (error) {
            console.log(error);
            setMessageError(error.response?.data?.message || 'An error occurred');
        }
    }

    const groupedByAnalyse = fitlerPAP.reduce((acc, pap) => {
        const analyseLibelle = analyses.filter(analyse => pap.codeAP.includes(analyse.codeAnalyse)).map(analyse => analyse.libelleAnalyse);

        if (!acc[analyseLibelle]) {
            acc[analyseLibelle] = [];
        }
        acc[analyseLibelle].push(pap);
        return acc;
    }, {});

    return <>
        <h1 className="text-4xl font-primaryBold dark:text-white">Plans de Contrôle</h1> <br />
        <div>
            <div className={`shadow-xl p-2 fixed w-[388px] h-[492px] bg-[#238899] rounded-xl ${display}`} style={{ transform: "translate(-50%, -50%)", left: "50%", top: "50%", zIndex: "999" }}>
                <svg onClick={() => setDisplay('hidden')} className="ml-auto w-[30px] h-[30px] cursor-pointer text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                <form className="w-full px-2" onSubmit={(e) => handleUpdateParam(e, updatedParamAnalyseProcess.codePAP)}>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white"> Analyse</label>
                        <input type="text" disabled onChange={handlChangeUpdatedParam} name="codeAP" value={analyses.filter(analyse => updatedParamAnalyseProcess.codeAP.includes(analyse.codeAnalyse)).map(analyse => analyse.libelleAnalyse)} className="font-primaryRegular bg-slate-400 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_parametreAnalyse" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">parametre d&apos;analyse</label>
                        <input type="text" disabled onChange={handlChangeUpdatedParam} name="codeParam" value={parametresData.filter(param => param.codeParam === updatedParamAnalyseProcess.codeParam).map(param => param.libelleParam)} className="font-primaryRegular bg-slate-400 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="code_parametreAnalyse" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Valeur Min</label>
                        <input type="number" step="0.1" onChange={handlChangeUpdatedParam} name="valeurMin" value={updatedParamAnalyseProcess.valeurMin} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_parametreAnalyse" required />
                    </div>
                    <div className="mb-5 w-full">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Valeur Max</label>
                        <input type="number" step="0.1" onChange={handlChangeUpdatedParam} name="valeurMax" value={updatedParamAnalyseProcess.valeurMax} className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_parametreAnalyse" required />
                    </div>
                    <div className="flex justify-center">
                        <button type="submit" className="font-primaryBold my-2 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Modifier</button>
                    </div>
                </form>
            </div>
            <p className="text-xl font-primaryMedium text-gray-900 dark:text-white" style={{ color: "#9A93B3" }}>Plan De Controle De Produit / {libelleProduit}</p> <br />
            <p className="text-xl font-primaryBold dark:text-white"><Link to={`/planDeControle/${plan.codePlan}`} className="underline underline-offset-1 decoration-black">Les Etapes De Process:</Link>   {etape}</p> <br />
            <div className="container mx-auto px-20">
                {messageError &&
                    <div className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span className="font-medium">Error!</span> {messageError}
                    </div>
                }
                <form className="flex items-center justify-center gap-x-6" onSubmit={handleSubmitAnalyseProcess}>
                    <div className="mb-5 w-96">
                        <p className="text-lg font-primaryBold dark:text-white">Sélectionner Les Analyses de Process</p>
                    </div>
                    <div className="mb-5 w-96">
                        <label className="font-primarySemiBold text-xl block mb-2 text-gray-900 dark:text-white">Analyse De Process</label>
                        <select onChange={handleChangeAnalyseProcess} value={analyseProcess.codeAnalyse} name="codeAnalyse" className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Libelle_Unite" required>
                            <option value='' disabled>Selectionnez une Analyse de Process</option>
                            {
                                analyses.map((analyse) => {
                                    return <option key={analyse.id} value={analyse.codeAnalyse}>{analyse.libelleAnalyse}</option>
                                })
                            }
                        </select>
                    </div>
                    <button type="submit" className="font-primaryBold mt-3.5 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Sélectionner</button>
                    <button onClick={handleCancelAnalyseProcess} className="font-primaryBold mt-3.5 text-white bg-[#ff0303] hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Annuler</button>
                </form>
                {
                    parametres.length === 0 ? ''
                        :
                        <div className="shadow-xl relative overflow-x-auto rounded-lg">
                            <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 font-primaryBold">
                                            Analyse
                                        </th>
                                        <th scope="col" className="px-6 py-3 font-primaryBold">
                                            Parametre
                                        </th>
                                        <th scope="col" className="px-6 py-3 font-primaryBold">
                                            unite
                                        </th>
                                        <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                            valeurMin
                                        </th>
                                        <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                            valeurMax
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {parametres.map((parametre, index) => (
                                        <tr key={index}>
                                            <td className="px-6 py-4 font-primaryMedium">{parametre.analyse.libelleAnalyse}</td>
                                            <td className="px-6 py-4 font-primaryMedium">{parametre.libelleParam}</td>
                                            <td className="px-6 py-4 font-primaryMedium">{parametre.unite_mesure.libelleUnite}</td>
                                            <td>
                                                <input className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-32 mx-auto"
                                                    type="number"
                                                    step="0.1"
                                                    name="valeurMin"
                                                    value={parametresAP[index]?.valeurMin || ''}
                                                    onChange={(e) => handleChangePAP(index, e)}
                                                    placeholder="valeur_min"
                                                    required
                                                />
                                            </td>
                                            <td>
                                                <input className="font-primaryRegular bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-32 mx-auto"
                                                    type="number"
                                                    step="0.1"
                                                    name="valeurMax"
                                                    value={parametresAP[index]?.valeurMax || ''}
                                                    onChange={(e) => handleChangePAP(index, e)}
                                                    placeholder="valeur_max"
                                                    required
                                                />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>

                        </div>
                }
                {
                    parametres.length === 0 ? ''
                        :
                        <button type="submit" onClick={handleSaveToDatabase} className="font-primaryBold mt-3.5 text-white bg-[#036EFF] hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">enregistré</button>
                }

                <br />

                {
                    fitlerPAP.length > 0 && (
                        <>
                            <hr className="h-px my-8 border-2 bg-gray-700"></hr>
                            <p className="text-xl font-primaryMedium text-gray-900 dark:text-white" style={{ color: "#9A93B3" }}>Liste Des Analyses</p> <br />
                            <div>

                                {
                                    Object.entries(groupedByAnalyse).map(([analyseLibelle, params], index) => (
                                        <div key={index}>
                                            <div className="flex justify-between items-center">
                                                <p className="font-primaryBold text-lg text-gray-900 dark:text-white mb-3">{analyseLibelle}</p>
                                                <button onClick={() => deleteParamAnalyseProcess(params)} className="font-primaryBold text-white mb-2.5 bg-[#ff0303] hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Supprimer l&apos;analyse</button>
                                            </div>
                                            <div className="shadow-xl relative overflow-x-auto rounded-lg mb-6">
                                                <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                    <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                        <tr>
                                                            <th scope="col" className="px-6 py-3 font-primaryBold">
                                                                Parametre
                                                            </th>
                                                            <th scope="col" className="px-6 py-3 font-primaryBold">
                                                                unite
                                                            </th>
                                                            <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                                                valeurMin
                                                            </th>
                                                            <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                                                valeurMax
                                                            </th>
                                                            <th scope="col" className="px-6 py-3 font-primaryBold text-center">
                                                                Actions
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {params.map((PAP, key) => (
                                                            <tr key={key} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                <td className="px-6 py-4 font-primaryMedium">
                                                                    {PAP.parametre_analyse.libelleParam}
                                                                </td>
                                                                <td className="px-6 py-4 font-primaryMedium">
                                                                    {unites.filter(unite => unite.codeUnite === PAP.parametre_analyse.codeUnite)
                                                                        .map(unite => unite.libelleUnite)
                                                                    }
                                                                </td>
                                                                <td className="px-6 py-4 font-primaryMedium text-center">
                                                                    {PAP.valeurMin}
                                                                </td>
                                                                <td className="px-6 py-4 font-primaryMedium text-center">
                                                                    {PAP.valeurMax}
                                                                </td>
                                                                <td className="px-6 py-4 flex justify-center gap-x-4">
                                                                    <button onClick={() => buttonModifier(PAP.codePAP)} className="font-primaryBold text-blue-600">modifier</button>
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br />
                                        </div>
                                    ))
                                }
                            </div>

                        </>
                    )
                }

            </div>
        </div>
    </>
}