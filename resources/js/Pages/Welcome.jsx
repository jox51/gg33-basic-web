import { Head, Link } from "@inertiajs/react";

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Welcome" />
            <div className="min-h-screen bg-gradient-to-b from-purple-900 via-indigo-900 to-slate-900">
                <header className="absolute top-0 z-50 w-full">
                    <nav className="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
                        <div className="flex lg:flex-1">
                            <a href="#" className="-m-1.5 p-1.5">
                                <span className="sr-only">GG33 Catch</span>
                                <div className="flex items-center gap-2">
                                    <div className="h-8 w-8 bg-cyan-400 rounded-full"></div>
                                    <h1 className="text-2xl font-bold text-white">
                                        GG33 Catch
                                    </h1>
                                </div>
                            </a>
                        </div>

                        <div className="-mx-3 flex flex-1 justify-end">
                            {auth.user ? (
                                <Link
                                    href={route("dashboard")}
                                    className="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-cyan-400 focus:outline-none"
                                >
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route("login")}
                                        className="rounded-md px-3 py-2 text-white hover:text-cyan-400 transition"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        href={route("register")}
                                        className="ml-4 rounded-md bg-cyan-500 px-3 py-2 text-white shadow-sm hover:bg-cyan-400 transition"
                                    >
                                        Start Matching
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </header>

                <main>
                    {/* Hero Section */}
                    <div className="relative isolate px-6 pt-14 lg:px-8">
                        <div className="mx-auto max-w-4xl py-32 sm:py-48 lg:py-56">
                            <div className="text-center">
                                <h1 className="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                                    Find Your Numerology Soulmate &
                                    <span className="block mt-4 text-cyan-400">
                                        Save Money Through Cosmic Alignment
                                    </span>
                                </h1>
                                <p className="mt-6 text-lg leading-8 text-gray-200">
                                    Our proprietary GG33 algorithms combine
                                    ancient numerology with modern matchmaking
                                    to help you find perfect compatibility while
                                    optimizing financial harmony.
                                </p>
                                <div className="mt-10 flex items-center justify-center gap-x-6">
                                    <Link
                                        href={route("register")}
                                        className="rounded-md bg-cyan-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-cyan-400 transition"
                                    >
                                        Discover Your Match
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* How It Works */}
                    <div className="bg-black/30 py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <h2 className="text-center text-3xl font-bold leading-8 text-white mb-16">
                                The GG33 Cosmic Connection Method
                            </h2>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                                {[
                                    {
                                        title: "Birth Analysis",
                                        desc: "Upload your birth details for our numerology engine to calculate your core numbers",
                                        icon: "M12 2v20M12 2l4 4m-4-4L8 6",
                                    },
                                    {
                                        title: "Cosmic Matching",
                                        desc: "We analyze 33 key numerological factors to find optimal financial & romantic matches",
                                        icon: "M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z",
                                    },
                                    {
                                        title: "Harmony Blueprint",
                                        desc: "Receive personalized strategies for relationship success and financial growth",
                                        icon: "M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z",
                                    },
                                ].map((item, idx) => (
                                    <div
                                        key={idx}
                                        className="bg-white/5 p-8 rounded-2xl backdrop-blur-sm"
                                    >
                                        <svg
                                            className="w-12 h-12 text-cyan-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d={item.icon}
                                            />
                                        </svg>
                                        <h3 className="mt-6 text-xl font-semibold text-white">
                                            {item.title}
                                        </h3>
                                        <p className="mt-2 text-gray-300">
                                            {item.desc}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Numerology Compatibility */}
                    <div className="relative isolate overflow-hidden bg-black/30 py-24">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                                <div>
                                    <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                                        Why Numerology Compatibility Matters
                                    </h2>
                                    <p className="mt-6 text-lg leading-8 text-gray-200">
                                        GG33's research shows that aligned Life
                                        Path numbers experience:
                                    </p>
                                    <ul className="mt-8 space-y-4">
                                        {[
                                            "73% higher relationship satisfaction",
                                            "62% better financial cooperation",
                                            "89% improved conflict resolution",
                                        ].map((item, idx) => (
                                            <li
                                                key={idx}
                                                className="flex items-center gap-3 text-gray-200"
                                            >
                                                <svg
                                                    className="w-5 h-5 text-cyan-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                </svg>
                                                {item}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="bg-gradient-to-br from-cyan-500 to-purple-600 p-1 rounded-2xl">
                                    <div className="bg-gray-900 rounded-xl p-8 h-full">
                                        <div className="aspect-w-16 aspect-h-9 bg-gray-800 rounded-lg overflow-hidden">
                                            {/* Add numerology chart image/component here */}
                                            <div className="w-full h-full flex items-center justify-center text-cyan-400">
                                                GG33 Compatibility Matrix
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* CTA Section */}
                    <div className="bg-black/30 py-24">
                        <div className="mx-auto max-w-4xl text-center">
                            <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                                Ready to Meet Your Numerological Match?
                            </h2>
                            <p className="mt-6 text-lg leading-8 text-gray-200">
                                Join thousands who've found love and financial
                                harmony through GG33's proven methods.
                            </p>
                            <div className="mt-10 flex items-center justify-center gap-x-6">
                                <Link
                                    href={route("register")}
                                    className="rounded-md bg-cyan-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-cyan-400 transition"
                                >
                                    Start Free Compatibility Analysis
                                </Link>
                            </div>
                        </div>
                    </div>
                </main>

                <footer className="border-t border-white/10">
                    <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <div className="h-8 w-8 bg-cyan-400 rounded-full"></div>
                                <p className="text-white font-semibold">
                                    GG33 Catch
                                </p>
                            </div>
                            <p className="text-gray-400">
                                &copy; {new Date().getFullYear()} GG33
                                Numerology Network
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
