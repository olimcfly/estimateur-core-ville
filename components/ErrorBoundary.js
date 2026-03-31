'use client';

import React from 'react';

export default class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null };
  }

  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }

  componentDidCatch(error, info) {
    if (typeof window !== 'undefined') {
      window.__LAST_ERROR__ = { error, info, timestamp: Date.now() };
    }

    if (this.props.onError) {
      this.props.onError(error, info);
    }

    // Sentry-ready hook
    if (typeof window !== 'undefined' && window.Sentry?.captureException) {
      window.Sentry.captureException(error, { extra: info });
    }
  }

  handleReset = () => {
    this.setState({ hasError: false, error: null });
    this.props.onReset?.();
  };

  render() {
    if (this.state.hasError) {
      return (
        <section role="alert" className="rounded-2xl border border-red-200 bg-red-50 p-6 text-center">
          <h2 className="text-xl font-semibold text-red-800">Oups, un bloc a rencontré une erreur.</h2>
          <p className="mt-2 text-sm text-red-700">Vous pouvez réessayer sans recharger toute la page.</p>
          <button
            type="button"
            onClick={this.handleReset}
            className="mt-4 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-700"
          >
            Réinitialiser ce bloc
          </button>
        </section>
      );
    }

    return this.props.children;
  }
}
