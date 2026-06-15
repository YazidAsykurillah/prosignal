import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function Show({ auth, project, latestGeneration, latestDiscoveryRun }) {
    const { flash } = usePage().props;
    const [isEditing, setIsEditing] = useState(false);
    
    const [formData, setFormData] = useState({
        industries: project.market_intelligence?.industries?.join(', ') || '',
        roles: project.market_intelligence?.roles?.join(', ') || '',
        company_sizes: project.market_intelligence?.company_sizes?.join(', ') || '',
        opportunity_signals: project.market_intelligence?.opportunity_signals?.join(', ') || '',
        discovery_keywords: project.market_intelligence?.discovery_keywords?.join(', ') || '',
    });

    useEffect(() => {
        let interval;
        if (latestGeneration?.status === 'pending' || latestGeneration?.status === 'processing' ||
            latestDiscoveryRun?.status === 'pending' || latestDiscoveryRun?.status === 'processing') {
            interval = setInterval(() => {
                router.reload({ only: ['project', 'latestGeneration', 'latestDiscoveryRun'] });
            }, 3000);
        }
        return () => {
            if (interval) clearInterval(interval);
        };
    }, [latestGeneration?.status, latestDiscoveryRun?.status]);

    useEffect(() => {
        if (project.market_intelligence) {
            setFormData({
                industries: project.market_intelligence.industries?.join(', ') || '',
                roles: project.market_intelligence.roles?.join(', ') || '',
                company_sizes: project.market_intelligence.company_sizes?.join(', ') || '',
                opportunity_signals: project.market_intelligence.opportunity_signals?.join(', ') || '',
                discovery_keywords: project.market_intelligence.discovery_keywords?.join(', ') || '',
            });
        }
    }, [project.market_intelligence]);

    const handleGenerate = () => {
        router.post(`/projects/${project.id}/market-intelligence/generate`);
    };

    const handleRunDiscovery = () => {
        router.post(`/projects/${project.id}/discoveries`);
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        
        const parseArray = (str) => str.split(',').map(s => s.trim()).filter(s => s.length > 0);
        
        router.put(`/projects/${project.id}/market-intelligence`, {
            industries: parseArray(formData.industries),
            roles: parseArray(formData.roles),
            company_sizes: parseArray(formData.company_sizes),
            opportunity_signals: parseArray(formData.opportunity_signals),
            discovery_keywords: parseArray(formData.discovery_keywords),
        }, {
            onSuccess: () => setIsEditing(false)
        });
    };

    const statusBadge = (status) => {
        const colors = {
            pending: 'bg-yellow-100 text-yellow-800',
            processing: 'bg-blue-100 text-blue-800',
            completed: 'bg-green-100 text-green-800',
            failed: 'bg-red-100 text-red-800'
        };
        return <span className={`px-2 py-1 text-xs font-semibold rounded-full ${colors[status] || 'bg-gray-100 text-gray-800'}`}>{status.toUpperCase()}</span>;
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">{project.name}</h2>}
        >
            <Head title={project.name} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {flash.success && (
                        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {flash.success}
                        </div>
                    )}
                    
                    {/* Project Details */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 className="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-gray-500">Industry</p>
                                <p className="font-medium">{project.industry || 'N/A'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Location</p>
                                <p className="font-medium">{project.location || 'N/A'}</p>
                            </div>
                            <div className="md:col-span-2">
                                <p className="text-sm text-gray-500">Description</p>
                                <p className="font-medium whitespace-pre-wrap">{project.description || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    {/* Market Intelligence */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-lg font-medium text-gray-900 flex items-center gap-3">
                                Market Intelligence
                                {latestGeneration && statusBadge(latestGeneration.status)}
                            </h3>
                            <div className="space-x-2">
                                <button 
                                    onClick={handleGenerate}
                                    disabled={latestGeneration?.status === 'pending' || latestGeneration?.status === 'processing'}
                                    className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {project.market_intelligence ? 'Regenerate AI' : 'Generate with AI'}
                                </button>
                                {project.market_intelligence && (
                                    <button 
                                        onClick={() => setIsEditing(!isEditing)}
                                        className="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300"
                                    >
                                        {isEditing ? 'Cancel Edit' : 'Edit Manually'}
                                    </button>
                                )}
                            </div>
                        </div>

                        {!project.market_intelligence && latestGeneration?.status !== 'processing' && (
                            <p className="text-gray-500 italic">No market intelligence generated yet. Click "Generate with AI" to start.</p>
                        )}
                        
                        {latestGeneration?.status === 'processing' && (
                            <div className="animate-pulse flex space-x-4">
                                <div className="flex-1 space-y-4 py-1">
                                    <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div className="space-y-2">
                                        <div className="h-4 bg-gray-200 rounded"></div>
                                        <div className="h-4 bg-gray-200 rounded w-5/6"></div>
                                    </div>
                                    <p className="text-blue-500 text-sm mt-4">AI is analyzing your project and generating intelligence...</p>
                                </div>
                            </div>
                        )}

                        {project.market_intelligence && !isEditing && (
                            <div className="space-y-6">
                                <div>
                                    <h4 className="font-medium text-md mb-2">Ideal Customer Profile (ICP)</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-md">
                                        <div>
                                            <p className="text-sm font-semibold text-gray-600">Industries</p>
                                            <ul className="list-disc list-inside text-sm mt-1">
                                                {project.market_intelligence.industries?.map((item, i) => <li key={i}>{item}</li>)}
                                            </ul>
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-gray-600">Roles</p>
                                            <ul className="list-disc list-inside text-sm mt-1">
                                                {project.market_intelligence.roles?.map((item, i) => <li key={i}>{item}</li>)}
                                            </ul>
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-gray-600">Company Sizes</p>
                                            <ul className="list-disc list-inside text-sm mt-1">
                                                {project.market_intelligence.company_sizes?.map((item, i) => <li key={i}>{item}</li>)}
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 className="font-medium text-md mb-2">Opportunity Signals</h4>
                                    <div className="bg-gray-50 p-4 rounded-md">
                                        <ul className="list-disc list-inside text-sm">
                                            {project.market_intelligence.opportunity_signals?.map((item, i) => <li key={i}>{item}</li>)}
                                        </ul>
                                    </div>
                                </div>

                                <div>
                                    <h4 className="font-medium text-md mb-2">Discovery Keywords</h4>
                                    <div className="flex flex-wrap gap-2">
                                        {project.market_intelligence.discovery_keywords?.map((item, i) => (
                                            <span key={i} className="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-md">{item}</span>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {isEditing && (
                            <form onSubmit={handleUpdate} className="space-y-4">
                                <p className="text-xs text-gray-500 mb-4">Enter values separated by commas.</p>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Industries</label>
                                    <input type="text" className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={formData.industries} onChange={e => setFormData({...formData, industries: e.target.value})} />
                                </div>
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Roles</label>
                                    <input type="text" className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={formData.roles} onChange={e => setFormData({...formData, roles: e.target.value})} />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Company Sizes</label>
                                    <input type="text" className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={formData.company_sizes} onChange={e => setFormData({...formData, company_sizes: e.target.value})} />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Opportunity Signals</label>
                                    <textarea className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={formData.opportunity_signals} onChange={e => setFormData({...formData, opportunity_signals: e.target.value})} />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Discovery Keywords</label>
                                    <textarea className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={formData.discovery_keywords} onChange={e => setFormData({...formData, discovery_keywords: e.target.value})} />
                                </div>

                                <div className="flex justify-end pt-4">
                                    <button type="submit" className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        )}
                    </div>
                    {/* Company Discovery Section */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-lg font-medium text-gray-900 flex items-center gap-3">
                                Company Discovery
                                {latestDiscoveryRun && statusBadge(latestDiscoveryRun.status)}
                            </h3>
                            <button 
                                onClick={handleRunDiscovery}
                                disabled={!project.market_intelligence || latestDiscoveryRun?.status === 'pending' || latestDiscoveryRun?.status === 'processing'}
                                className="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-md shadow-sm hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 transition-all duration-200"
                            >
                                Run Discovery
                            </button>
                        </div>

                        {!project.market_intelligence && (
                            <p className="text-gray-500 italic">Generate Market Intelligence first before running discovery.</p>
                        )}

                        {latestDiscoveryRun?.status === 'processing' && (
                            <div className="animate-pulse space-y-4">
                                <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                                <div className="h-4 bg-gray-200 rounded w-full"></div>
                                <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                                <p className="text-indigo-500 text-sm mt-4 font-medium">AI is scouring the web and analyzing companies for signals...</p>
                            </div>
                        )}

                        {latestDiscoveryRun && latestDiscoveryRun.status !== 'processing' && (
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                <div className="bg-indigo-50 rounded-lg p-4 border border-indigo-100 shadow-sm">
                                    <p className="text-sm text-indigo-600 font-semibold mb-1">Keywords Searched</p>
                                    <p className="text-2xl font-bold text-indigo-900">{latestDiscoveryRun.total_keywords}</p>
                                </div>
                                <div className="bg-purple-50 rounded-lg p-4 border border-purple-100 shadow-sm">
                                    <p className="text-sm text-purple-600 font-semibold mb-1">URLs Processed</p>
                                    <p className="text-2xl font-bold text-purple-900">{latestDiscoveryRun.total_urls}</p>
                                </div>
                                <div className="bg-emerald-50 rounded-lg p-4 border border-emerald-100 shadow-sm">
                                    <p className="text-sm text-emerald-600 font-semibold mb-1">Companies Discovered</p>
                                    <p className="text-2xl font-bold text-emerald-900">{latestDiscoveryRun.total_companies}</p>
                                </div>
                            </div>
                        )}

                        {project.discoveries && project.discoveries.length > 0 && (
                            <div>
                                <h4 className="font-medium text-md mb-4 text-gray-800">Discovered Companies</h4>
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg shadow-sm">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signal</th>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {project.discoveries.map((discovery, idx) => (
                                                <tr key={idx} className="hover:bg-gray-50 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="font-medium text-gray-900">{discovery.company?.company_name}</div>
                                                        <div className="text-sm text-gray-500">{discovery.company?.location || 'Unknown Location'}</div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="text-sm text-gray-900 line-clamp-2">{discovery.signal}</div>
                                                        <div className="text-xs text-gray-500 mt-1 truncate max-w-xs" title={discovery.summary}>{discovery.summary || 'No summary'}</div>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center">
                                                            <div className="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                                <div className={`h-2.5 rounded-full ${discovery.confidence_score > 70 ? 'bg-green-500' : 'bg-yellow-500'}`} style={{ width: `${discovery.confidence_score}%` }}></div>
                                                            </div>
                                                            <span className="text-sm text-gray-700">{discovery.confidence_score}%</span>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 text-sm font-medium">
                                                        <a href={discovery.source_url} target="_blank" rel="noreferrer" className="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                                                            View Source
                                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                            </svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
