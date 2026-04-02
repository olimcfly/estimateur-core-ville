/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  outputFileTracingRoot: '/home/sc1tasq5564/test-estimateur',
  experimental: {
    workerThreads: false,
    cpus: 1,
  },
};

module.exports = nextConfig;
